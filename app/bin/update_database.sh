#!/bin/bash

if [ ! $path_app ]; then
	path=`dirname $(readlink -f $0)`
	path=`dirname ${path}`
	source ${path}/lib/scripts/paths.sh
fi

##################################################
echo ${hr}
echo
echo "Updating the database";
echo ${hr}

echo "Create new tables, if any";
${cmd_schema} create

echo "Update the existing tables";
${cmd_schema} update