SRC_DIR=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
SUBDOMAIN=${USER}
WEB_ROOT_DIR=/data/web/personal/${SUBDOMAIN}/www_91jili_com

test:
	/usr/local/bin/phpunit -c ./app/ -d memory_limit=-1 -v --debug | tee /tmp/report

assets-rebuild:
	php ./app/console assets:install web --symlink --relative

deploy-js-routing: assets-rebuild
	./app/console	fos:js-routing:dump

setup: show-setting setup-submodules create-dir fix-perms create-config create-symlinks cc-all

setup-submodules:
	@# No access to "local-git" from vagrant environment
	@if [ "$(USER)" = "vagrant" ] || [ "$(USER)" = "ubuntu" ] ; then \
		for mod in $$(find ./submodules/ -maxdepth 1 -mindepth 1 | grep -v local-git); do \
			git submodule update --init $$mod; \
		done \
	else \
		git submodule update --init; \
	fi;


show-setting:
	@echo "Setting"
	@echo "-> SRC_DIR=${SRC_DIR}"
	@echo "-> SUBDOMAIN=${SUBDOMAIN}"
	@echo "-> WEB_ROOT_DIR=${WEB_ROOT_DIR}"

create-dir:
	mkdir -p ${WEB_ROOT_DIR}
	mkdir -p app/{cache,cache_data,logs,logs_data,sessions} web/images/actionPic

fix-perms:
	sudo chgrp -R apache app/cache
	sudo chgrp -R apache app/cache_data
	sudo chgrp -R apache app/logs
	sudo chgrp -R apache web/images/actionPic
	sudo chmod -R g+w  app/cache
	sudo chmod -R g+w  app/cache_data
	sudo chmod -R g+w  app/logs
	sudo chmod -R g+w   web/images/actionPic
	sudo chgrp -R apache app/sessions
	sudo chmod -R g+w  app/sessions
	sudo chgrp -R apache app/logs_data
	sudo chmod -R g+w  app/logs_data

create-config:
	cp -n ${SRC_DIR}/app/config/custom_parameters.yml.dist ${SRC_DIR}/app/config/custom_parameters.yml
	cp -n ${SRC_DIR}/app/config/config_dev.yml.dist        ${SRC_DIR}/app/config/config_dev.yml
	cp -n ${SRC_DIR}/app/config/config_test.yml.dist       ${SRC_DIR}/app/config/config_test.yml
	cp -n ${SRC_DIR}/app/config/parameters.yml.dist        ${SRC_DIR}/app/config/parameters.yml

create-symlinks:
	ln -fs ${SRC_DIR}/web ${WEB_ROOT_DIR}/

fix-777:
	sudo chgrp -R apache app/cache
	sudo chgrp -R apache app/cache_data
	sudo chgrp -R apache app/logs
	sudo chgrp -R apache web/images/actionPic
	sudo chmod -R 777  app/cache
	sudo chmod -R 777  app/cache_data
	sudo chmod -R 777  app/logs
	sudo chmod -R 777   web/images/actionPic
	sudo chgrp -R apache app/sessions
	sudo chmod -R 777  app/sessions
	sudo chgrp -R apache app/logs_data
	sudo chmod -R 777  app/logs_data

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


