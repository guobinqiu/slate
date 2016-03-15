DEVELOPER_DIRECTORY=/data/web/${USER}/1

test:
	/usr/local/bin/phpunit -c ./app/ -d memory_limit=-1 -v --debug | tee /tmp/report

assets-rebuild:
	php ./app/console assets:install web --symlink --relative

deploy-js-routing: assets-rebuild
	./app/console	fos:js-routing:dump

setup: create-dir fix-perms create-config cc-all

create-dir:
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
	cp -n ${DEVELOPER_DIRECTORY}/app/config/custom_parameters.yml.dist ${DEVELOPER_DIRECTORY}/app/config/custom_parameters.yml
	cp -n ${DEVELOPER_DIRECTORY}/app/config/config_dev.yml.dist        ${DEVELOPER_DIRECTORY}/app/config/config_dev.yml
	cp -n ${DEVELOPER_DIRECTORY}/app/config/config_test.yml.dist       ${DEVELOPER_DIRECTORY}/app/config/config_test.yml
	cp -n ${DEVELOPER_DIRECTORY}/app/config/parameters.yml.dist        ${DEVELOPER_DIRECTORY}/app/config/parameters.yml

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


