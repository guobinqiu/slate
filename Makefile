########################################################################
# You can study on http://mrbook.org/blog/tutorials/make/
# Do not use phpstorm or similar IDE to edit this file!
# Pay attention to the format of Makefiles below:
#
#     target: dependencies
#     [tab] system command
#
########################################################################

APACHEUSER=$(shell ps aux | grep -E '[a]pache|[h]ttpd' | grep -v root | head -1 | cut -d\  -f1)
BRANCH=origin/master
PHP=$(shell which php)
PHPUNIT=./bin/phpunit
SRC_DIR=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
SUBDOMAIN=${USER}
WEB_ROOT_DIR=/data/web/personal/${SUBDOMAIN}/www_91jili_com

test:
	$(PHPUNIT) -c ./app/phpunit.xml --testsuite all -d memory_limit=-1 --debug --verbose

test-data: setup-databases
	mysql -uroot jili_dev < sql/static_data/provinceList.sql
	mysql -uroot jili_dev < sql/static_data/cityList.sql
	mysql -uroot jili_dev < sql/static_data/points_exchange_type.sql
	mysql -uroot jili_dev < sql/static_data/prize_items.sql
	mysql -uroot jili_dev < sql/dev_data/user.sql
	mysql -uroot jili_dev < sql/dev_data/ssi_project.sql
	mysql -uroot jili_dev < sql/dev_data/ssi_respondent.sql
	mysql -uroot jili_dev < sql/dev_data/ssi_project_respondent.sql

test-branch: cc-all
	git diff --name-only ${BRANCH}... --diff-filter=AM src/ | grep "Test.php" | xargs -n 1 $(PHPUNIT) -c ./app/ -d memory_limit=-1

assets-rebuild:
	php ./app/console assets:install web --symlink --relative

deploy-js-routing: assets-rebuild
	./app/console	fos:js-routing:dump
	./app/console	assetic:dump --env=test

setup: show-setting setup-submodules create-dir fix-perms test-data deploy-js-routing cc-all setup-web-root

setup-perl:
	cd ${SRC_DIR}/scripts/perl/ && $(MAKE) setup

setup-databases:
	php app/console doctrine:database:drop --force --env "dev" --if-exists
	php app/console doctrine:database:create --env "dev"
	php app/console doctrine:schema:update --force --env "dev"

setup-circle-databases:
	php app/console doctrine:database:drop --force --env "test" --if-exists
	php app/console doctrine:database:create --env "test"
	php app/console doctrine:schema:update --force --env "test"

setup-submodules:
	git submodule update --init;

circle: setup-submodules create-dir fix-perms deploy-js-routing cc-all setup-circle-databases

show-setting:
	@echo "Setting"
	@echo "-> SRC_DIR=${SRC_DIR}"
	@echo "-> SUBDOMAIN=${SUBDOMAIN}"
	@echo "-> WEB_ROOT_DIR=${WEB_ROOT_DIR}"

create-dir:
	mkdir -p app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads
	sudo mkdir -p /data/91jili/logs

setup-web-root:
	mkdir -p ${WEB_ROOT_DIR}
	ln -fs ${SRC_DIR}/web ${WEB_ROOT_DIR}/

fix-perms:
	@if [ "$(USER)" = "vagrant" ] || [ "$(USER)" = "ubuntu" ] ; then \
		sudo setfacl -R -m u:"${APACHEUSER}":rwX -m u:${USER}:rwX app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads /data/91jili/logs ; \
		sudo setfacl -dR -m u:"${APACHEUSER}":rwX -m u:${USER}:rwX app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads /data/91jili/logs ; \
	else \
		sudo chgrp -R apache app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads /data/91jili/logs ; \
		sudo chmod -R g+w app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads /data/91jili/logs ; \
	fi;

fix-777:
	sudo chgrp -R apache app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads /data/91jili/logs
	sudo chmod -R 777  app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads /data/91jili/logs

deploy: deploy-js-routing
	@echo done

cc-cache:
	sudo rm -rf app/cache/*

cc-all:
	sudo rm -rf app/cache/*
	sudo rm -rf app/cache_data/*
	sudo rm -rf app/logs/*
	sudo rm -rf app/logs_data/*
	sudo rm -rf app/sessions/*

sass:
	sass -v
	sass --trace  -C --sourcemap=none  --style=nested --watch web/sass:web/css

run-job:
	php ./app/console jms-job-queue:run  --env dev -j 4

php_code_sniffer:
	git diff --diff-filter=AMR --name-only origin/master | grep -v 'vendor' | xargs php vendor/squizlabs/php_codesniffer/scripts/phpcbf --standard=./ruleset.xml

api-doc:
    scp app/docs/api/index.html.md wenwen@192.168.1.212:/home/wenwen/slate/source/index.html.md