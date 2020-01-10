#!/bin/bash

yum install -y php71-mbstring.x86_64 zip php71-pgsql php71-mysqli

mkdir /tmp/layer
cd /tmp/layer
cp /opt/layer/bootstrap .
sed "s/PHP_MINOR_VERSION/1/g" /opt/layer/php.ini >php.ini

mkdir bin
cp /usr/bin/php bin/

mkdir lib
for lib in libncurses.so.5 libtinfo.so.5 libpcre.so.0; do
  cp "/lib64/${lib}" lib/
done

cp /usr/lib64/libedit.so.0 lib/
cp /usr/lib64/libpq.so.5 lib/

cp -a /usr/lib64/php lib/

TARGET_NAME=php71
if [ "${GENERAL_EVENT}" = "true" ]; then
  TARGET_NAME=${TARGET_NAME}g

  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php composer-setup.php
  php -r "unlink('composer-setup.php');"
  ./composer.phar global require aws/aws-sdk-php
  ./composer.phar global clear-cache
  cp -a /root/.composer lib/composer
  cp /opt/layer/php.ini.generalenv php.ini
  mv lib/php/7.1/* lib/php/
  rmdir lib/php/7.1
fi

zip -r /opt/layer/${TARGET_NAME}.zip .
