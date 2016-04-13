APACHEUSER=$(shell ps aux | grep -E '[a]pache|[h]ttpd' | grep -v root | head -1 | cut -d\  -f1)
BRANCH=origin/master
PHP=$(shell which php)
PHPUNIT=./bin/phpunit
SRC_DIR=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
SUBDOMAIN=${USER}
WEB_ROOT_DIR=/data/web/personal/${SUBDOMAIN}/www_91jili_com

test:
	$(PHPUNIT) -c ./app/ -d memory_limit=-1 --testsuite wenwen

test-data: cc-all
	yes | $(PHP) app/console doctrine:fixtures:load --fixtures=./src/Jili/FrontendBundle/DataFixtures/ORM/DummyData/

test-branch: cc-all
	git diff --name-only ${BRANCH}... --diff-filter=AM src/ | grep "Test.php" | xargs -n 1 $(PHPUNIT) -c ./app/ -d memory_limit=-1 | tee /tmp/report

assets-rebuild:
	php ./app/console assets:install web --symlink --relative

deploy-js-routing: assets-rebuild
	./app/console	fos:js-routing:dump

setup: show-setting setup-submodules create-dir fix-perms create-config setup-databases deploy-js-routing cc-all setup-web-root

setup-databases:
	@if [ "$(USER)" = "vagrant" ] || [ "$(USER)" = "ubuntu" ] ; then \
		if [ `mysql -uroot -e  "SHOW DATABASES" | grep "jili_db"` ] ; then \
			php app/console doctrine:database:drop --force; \
		fi; \
		php app/console doctrine:database:create; \
		php app/console doctrine:schema:update --force; \
	fi;

setup-submodules:
	git submodule update --init;

circle: setup-submodules create-dir create-config fix-perms deploy-js-routing cc-all
	sed -ie "s/root/ubuntu/g" ${SRC_DIR}/app/config/config_test.yml
	sed -ie "s/jili_test/circle_test/g" ${SRC_DIR}/app/config/config_test.yml

show-setting:
	@echo "Setting"
	@echo "-> SRC_DIR=${SRC_DIR}"
	@echo "-> SUBDOMAIN=${SUBDOMAIN}"
	@echo "-> WEB_ROOT_DIR=${WEB_ROOT_DIR}"

create-dir:
	mkdir -p app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads web/uploads/user
	sudo mkdir -p /data/91jili/logs/admin

setup-web-root:
	mkdir -p ${WEB_ROOT_DIR}
	ln -fs ${SRC_DIR}/web ${WEB_ROOT_DIR}/

fix-perms:
	@if [ "$(USER)" = "vagrant" ] || [ "$(USER)" = "ubuntu" ] ; then \
		sudo setfacl -R -m u:"${APACHEUSER}":rwX -m u:${USER}:rwX app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads web/uploads/user /data/91jili/logs/admin ; \
		sudo setfacl -dR -m u:"${APACHEUSER}":rwX -m u:${USER}:rwX app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads web/uploads/user /data/91jili/logs/admin ; \
	else \
		sudo chgrp -R apache app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads web/uploads/user /data/91jili/logs/admin ; \
		sudo chmod -R g+w app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads web/uploads/user /data/91jili/logs/admin ; \
	fi;

create-config:
	cp -n ${SRC_DIR}/app/config/custom_parameters.yml.dist ${SRC_DIR}/app/config/custom_parameters.yml
	cp -n ${SRC_DIR}/app/config/config_dev.yml.dist        ${SRC_DIR}/app/config/config_dev.yml
	cp -n ${SRC_DIR}/app/config/config_test.yml.dist       ${SRC_DIR}/app/config/config_test.yml
	cp -n ${SRC_DIR}/app/config/parameters.yml.dist        ${SRC_DIR}/app/config/parameters.yml

fix-777:
	sudo chgrp -R apache app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads web/uploads/user /data/91jili/logs/admin
	sudo chmod -R 777  app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic web/images web/uploads web/uploads/user /data/91jili/logs/admin

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


