#!/bin/bash

yum install -y php71-mbstring.x86_64 zip php71-pgsql php71-mysqli

mkdir /tmp/layer
cd /tmp/layer
cp /opt/layer/bootstrap .
cp /opt/layer/php.ini .

mkdir bin
cp /usr/bin/php bin/

mkdir lib
for lib in libncurses.so.5 libtinfo.so.5 libpcre.so.0; do
  cp "/lib64/${lib}" lib/
done

cp /usr/lib64/libedit.so.0 lib/

cp -a /usr/lib64/php lib/

zip -r /opt/layer/php71.zip .