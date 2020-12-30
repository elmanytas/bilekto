#!/bin/bash

# Copy config.ini.dist to config.ini configuring all vars with
# environment vars.

cp /var/www/html/config.ini.dist /var/www/html/config.ini
echo $GALLERY_NAME
if [ ! -z "$GALLERY_NAME" ]; then
  sed -i "s|gallery_name.*|gallery_name = \"$GALLERY_NAME\"|g" /var/www/html/config.ini
fi
if [ ! -z "$ALLOW_DOWNLOADS" ]; then
  sed -i "s|allow_downloads.*|allow_downloads = \"$ALLOW_DOWNLOADS\"|g" /var/www/html/config.ini
fi
if [ ! -z "$ESTIMATE_ARCHIVE_SIZE"]; then
  sed -i "s|estimate_archive_size.*|estimate_archive_size = \"$ESTIMATE_ARCHIVE_SIZE\"|g" /var/www/html/config.ini
fi
if [ ! -z "$CACHING" ]; then
  sed -i "s|caching.*|caching = \"$CACHING\"|g" /var/www/html/config.ini
fi
if [ ! -z "$THUMB_FORMAT" ]; then
  sed -i "s|thumb_format.*|thumb_format = \"$THUMB_FORMAT\"|g" /var/www/html/config.ini
fi

/usr/sbin/apache2ctl -D FOREGROUND
