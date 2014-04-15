help:
	@echo  'php-cs'
	@echo  'cc'
	@echo  'change-perms'

#
php-cs:
	php-cs-fixer --config=sf23 --dry-run fix . 
	

# cc: clear cach and logs.
cc:
	sudo rm -rf app/cache/*
	sudo rm -rf app/logs/*

# change-perms: update the app/logs app/cache permissions.
fix-perms:
	sudo chgrp -R www-data app/cache
	sudo chgrp -R www-data app/logs
	sudo chgrp -R www-data web/images/actionPic
	sudo chmod -R g+w  app/cache
	sudo chmod -R g+w  app/logs
	sudo chmod -R g+w   web/images/actionPic 
hw:
	@echo `pwd`;
#rsync-local:
#	@echo ok
#	rsync  -n -azC --force --chmod g+rwx --delete  --progress --exclude-from="./config/rsync_exclude.txt"  --include-from="./config/rsync_include.txt"  \
#  ./  /home/tau/voyagechina/Kotobank/kbankd1/kotobank.lib/sfproject/batch/dd_yahoo/
#
#rsync-kbank12:
#	@echo ok
#	rsync  -azC --force --chmod g+rwx --delete  --progress --exclude-from="./config/rsync_exclude.txt"  --include-from="./config/rsync_include.txt" -e "ssh -p22" \
#  ./ "kotobanksys@kbank12.kotobank.jp:~/dd_yahoo/"
#
#rsync-kbankd1:
#	@echo ok
#	rsync  -azC --force --chmod g+rwx --delete  --progress --exclude-from="./config/rsync_exclude.txt"   -e "ssh -p22" \
#  ./ "kotobankecnavi@kbankd1:/home/kotobankecnavi/dd_yahoo/"

rsync-voyagechina:
	@echo ok
	rsync   -azC --force --chmod g+rwx --delete  --progress --cvs-exclude --include-from="./config/rsync_include.txt" --exclude-from="./config/rsync_exclude.txt" -e "ssh -p22" \
  ./ "jiangtao@voyagechina:/var/www/html/jili-jarod/"


rsync-vct-jiang:
	@echo ok
	rsync   -azC --force --chmod g+rwx --delete  --progress --cvs-exclude --exclude-from="./config/rsync_exclude_vct32.txt"  --include-from="./config/rsync_include_vct32.txt" -e "ssh -p22" \
  ./ "tau@voyagechina-jiang:~/voyagechina/PointMedia/0129_rsync/PointMedia/"

test:
	/usr/local/bin/phpunit -c ./app/
#	bin/phpunit -c ./app/

test-ad:
	phpunit -c app  src/Jili/ApiBundle/Tests/Controller/AdvertisermentControllerTest.php

test-util:
	phpunit -c app  src/Jili/ApiBundle/Tests/Utility/StringTest.php

test-unit-backend:
	phpunit -c app src/Jili/BackendBundle/Tests/Component/Chanet/

test-emar:
	phpunit -c app src/Jili/EmarBundle/Tests/Controller/ApiControllerTest.php

test-offerwow:
	phpunit -c app src/Jili/ApiBundle/Tests/Controller/OfferwowControllerTest.php

fix-doctrine:
	ln -s ../vendor/doctrine/orm/bin/doctrine bin/doctrine

git-untrace:
	git update-index --assume-unchanged app/SymfonyRequirements.php
	git update-index --assume-unchanged app/config/parameters.yml
	git update-index --assume-unchanged bin/doctrine
	git update-index --assume-unchanged bin/doctrine.php
	git update-index --assume-unchanged composer.lock
	git update-index --assume-unchanged .gitignore
	git update-index --assume-unchanged	web/bundles/

git-retrace:
	git update-index --no-assume-unchanged app/SymfonyRequirements.php
	git update-index --no-assume-unchanged app/config/parameters.yml
	git update-index --no-assume-unchanged bin/doctrine
	git update-index --no-assume-unchanged bin/doctrine.php
	git update-index --no-assume-unchanged composer.lock
	git update-index --no-assume-unchanged .gitignore
	git update-index --no-assume-unchanged 	web/bundles/

gen-crud:
	./app/console doctrine:generate:crud --entity=JiliEmarBundle:EmarWebsites --route-prefix=admin/emar/websites --with-write  --overwrite
#adjust-html:
#	sed -i 's/src="other140307/src="{{ assets(\'other140307/g' 
#	--wid --cat --remove --update

emar-products-update-all-count:
	./app/console emar:products --update-all-count &

# update 
emar-products-update-all:
	./app/console emar:products --update-all| tee /tmp/logs &

emar-products-update:
	./app/console emar:products 3414 101030000 --update

emar-products-remove:
	./app/console emar:products --remove  

# update 
emar-websites-update:
	./app/console emar:websites --update  

emar-websites-update-start:
	./app/console emar:websites --update --start  470 
#				"web_id": "3414",
#				"web_name": "史泰博",
#				"ori_price": "128.0",
#				"cur_price": "118.0",
#				"pic_url": "http://S05.staples.cn:80/ftp_product_img/cn0107010010006_1_enl.jpg",
#				"catid": "101030000",
comment-debugs:
	find src/Jili/  -type f -name "*.php" -exec sed -i "s/\(^[^#][^#]*debug('{jarod.*$\)/# \1/g" {} \;
	find src/Jili/  -type f -name "*.php" | xargs grep -inE "^[^#][^#]*debug\('{jarod}" | wc -l
	find src/Jili/  -type f -name "*.php" -exec sed -i "s/\(^[^#][^#]*debug('{jarod.*$\)/# \1/g" {} \;
	find src/Jili/  -type f -name "*.php" -exec sed -i "s/\(^[^#][^#]*('{jarod.*$\)/# \1/g" {} \;
