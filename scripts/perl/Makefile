setup:
	echo "development-${USER}" >env.pl
	carton install --deployment
test:
	carton exec -- prove -ILib -Ibin/local -r t/
