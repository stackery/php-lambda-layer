#!/bin/bash

yum install -y wget
yum install -y yum-utils
wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
wget https://rpms.remirepo.net/enterprise/remi-release-6.rpm
rpm -Uvh remi-release-6.rpm
rpm -Uvh epel-release-latest-6.noarch.rpm


yum-config-manager --enable remi-php72

yum install -y httpd
yum install -y postgresql-devel
yum install -y libargon2-devel

yum install -y --disablerepo="*" --enablerepo="remi,remi-php72" php php-mbstring php-pdo php-mysql php-pgsql


mkdir /tmp/layer
cd /tmp/layer
cp /opt/layer/bootstrap-php72 bootstrap
cp /opt/layer/php72.ini php.ini

mkdir bin
cp /usr/bin/php bin/

mkdir lib
for lib in libncurses.so.5 libtinfo.so.5 libpcre.so.0; do
  cp "/lib64/${lib}" lib/
done

cp /usr/lib64/libedit.so.0 lib/
cp /usr/lib64/libargon2.so.0 lib/
cp /usr/lib64/libpq.so.5 lib/

cp -a /usr/lib64/php lib/

zip -r /opt/layer/php72.zip .

