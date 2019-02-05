#!/bin/bash -e

PHP_VERSION="${1}"
LAYER_VERSION="${2}"

source regions.sh

LAYER="php${PHP_VERSION//.}"
MD5SUM=$(md5 -q "${LAYER}.zip")
S3KEY="${LAYER}/${MD5SUM}"

for region in "${PHP_REGIONS[@]}"; do
  bucket_name="stackery-layers-${region}"

  echo "Deleting Lambda Layer ${LAYER} version ${VERSION} in region ${region}..."
  aws --region $region lambda delete-layer-version --layer-name ${LAYER} --version-number $VERSION > /dev/null
  echo "Deleted Lambda Layer ${LAYER} version ${VERSION} in region ${region}"
done
