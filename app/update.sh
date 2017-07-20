#!/bin/bash

if [ ! $path_app ]; then
	path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
	path=`dirname ${path}`
	source ${path}/lib/scripts/paths.sh
fi

echo
echo "Updating the application";
echo

### Update the database
source ${path_bin}update_database.sh


### Update the crontab
source ${path_bin}update_cron.sh


### Update the database records, if needed
source ${path_bin}update_db_records.sh



