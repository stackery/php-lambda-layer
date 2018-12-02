#!/bin/bash

yum install -y php71-cli zip php71-devel php71-mbstring.x86_64 php71-mcrypt.x86_64 php71-pdo.x86_64 php71-pecl-redis.x86_64 php71-mysqlnd.x86_64
cd /tmp
git clone --depth=1 "git://github.com/phalcon/cphalcon.git"
cd cphalcon/build && ./install
rm -rf /tmp/cphalcon

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
