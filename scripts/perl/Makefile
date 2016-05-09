setup: create-config carton-install

carton-install:
	carton install --deployment

create-config:
	if [ ! -e env.pl ] ; then \
		touch env.pl ; \
		echo '$$ENV{WENWEN_ENV} = "development-$(USER)";' > env.pl; \
	fi;

test:
	carton exec -- prove -ILib -r t/
