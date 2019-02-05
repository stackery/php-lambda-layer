#!/bin/bash -e

source regions.sh

PHP_VERSION="${1}"

LAYER="php${PHP_VERSION//.}"
MD5SUM=$(md5 -q "${LAYER}.zip")
S3KEY="${LAYER}/${MD5SUM}"

for region in "${PHP_REGIONS[@]}"; do
  bucket_name="stackery-layers-${region}"

  echo "Uploading ${LAYER}.zip to s3://${bucket_name}/${S3KEY}"

  aws --region $region s3 cp ${LAYER}.zip "s3://${bucket_name}/${S3KEY}"
done
