#!/bin/env node
#!/bin/bash

sourceFolder=$1   	#'pub/static/frontend/Pearl/weltpixel/en_US_source/'
oriFolder=$2		#'pub/static/frontend/Pearl/weltpixel/en_US/'

if [[ $1 && $2 ]]; then
	
	if [ ! -d "$sourceFolder" ]; then
		mv $oriFolder $sourceFolder
	fi

	{ # try
		if [[ $3 ]]; then
	    	$3 'pub/js-bundle/dist/r.js' -o 'pub/js-bundle/build/build.js' baseUrl=$1 dir=$2
	    else
	    	nodejs 'pub/js-bundle/dist/r.js' -o 'pub/js-bundle/build/build.js' baseUrl=$1 dir=$2
	    fi
	} || { # catch
	    echo "Generating the bundles failed"
	}
	
else
	echo "ERROR: parameters source static folder and original static folder are required"
fi