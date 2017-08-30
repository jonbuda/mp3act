#!/bin/sh
#
# Simple script to walk through a folder and load all of the data into mp3act.
# The benefit of this method is that you don't need to dump your database
# first, so you can schedule this via cron to run nightly
#

# Base URL you use to access mp3act
URL="https://localhost/mp3act"

# Base filesystem folder containing all your music.  
FOLDER="/music"

# By default this script will make an "Add" call for each folder.  This is
# useful if you have a lot of music and a few dozen folders it's stored in.  
for DIR in `ls -1 $FOLDER`; do 
	echo -n "$DIR: "
	curl -k -s "$URL/add.php?musicpath=$FOLDER/$DIR" 2> /dev/null | \
		tee /var/log/mp3act-add.log | \
		grep "To The Database" | sed -e 's/.*Added/Added/g' | \
		sed -e 's/Database.*/Database/g' | sed -e 's/<strong>//g' | \
		sed -e 's/<\/strong>//g'
done
