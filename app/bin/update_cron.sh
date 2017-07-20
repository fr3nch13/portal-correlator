#!/bin/bash

if [ ! $path_app ]; then
	path=`dirname $(readlink -f $0)`
	path=`dirname ${path}`
	source ${path}/lib/scripts/paths.sh
fi

##################################################
echo ${hr}
echo
echo "Installing the crontab";
echo ${hr}

echo "Replacing the variables";
VAR='\[path_bin\]'
VAL=${path_bin}
VAL="${VAL//\//\\/}"
# this will also copy the edited version to the tmp directory
# and leave the original template un touched
sed "s/${VAR}/${VAL}/g" "${path_config}crontab.txt" > "${path_tmp}crontab"

VAR='\[path_logs\]'
VAL=${path_logs}
VAL="${VAL//\//\\/}"
# note the '-i' and that it is editing the tmp file directly
sed -i "s/${VAR}/${VAL}/g" "${path_tmp}crontab"

echo "Moving the crontab into place using the 'crontab' command";
crontab "${path_tmp}crontab"

echo "Removing the tmp file"
rm "${path_tmp}crontab"