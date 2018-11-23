#!/bin/bash -e

VERSION=$1

source regions.sh

MD5SUM=$(md5 -q php71.zip)
S3KEY="php71/${MD5SUM}"

for region in "${PHP71_REGIONS[@]}"; do
  bucket_name="stackery-layers-${region}"

  echo "Deleting Lambda Layer php71 version ${VERSION} in region ${region}..."
  aws --region $region lambda delete-layer-version --layer-name php71 --version-number $VERSION > /dev/null
  echo "Deleted Lambda Layer php71 version ${VERSION} in region ${region}"
done
