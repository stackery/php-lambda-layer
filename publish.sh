#!/bin/bash -e

source regions.sh

PHP_VERSION="${1}"

LAYER="php${PHP_VERSION//.}"
MD5SUM=$(md5 -q "${LAYER}.zip")
S3KEY="${LAYER}/${MD5SUM}"

for region in "${PHP_REGIONS[@]}"; do
  bucket_name="stackery-layers-${region}"

  echo "Publishing Lambda Layer ${LAYER} in region ${region}..."
  # Must use --cli-input-json so AWS CLI doesn't attempt to fetch license URL
  version=$(aws --region $region lambda publish-layer-version --cli-input-json "{\"LayerName\": \"${LAYER}\",\"Description\": \"PHP ${PHP_VERSION} Web Server Lambda Runtime\",\"Content\": {\"S3Bucket\": \"${bucket_name}\",\"S3Key\": \"${S3KEY}\"},\"CompatibleRuntimes\": [\"provided\"],\"LicenseInfo\": \"http://www.php.net/license/3_01.txt\"}"  --output text --query Version)
  echo "Published Lambda Layer ${LAYER} in region ${region} version ${version}"

  echo "Setting public permissions on Lambda Layer ${LAYER} version ${version} in region ${region}..."
  aws --region $region lambda add-layer-version-permission --layer-name "${LAYER}" --version-number $version --statement-id=public --action lambda:GetLayerVersion --principal '*' > /dev/null
  echo "Public permissions set on Lambda Layer ${LAYER} version ${version} in region ${region}"
done
