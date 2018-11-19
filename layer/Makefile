ROOT_DIR:=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

php71.zip:
	rm -f php71.zip
	echo $(ROOT_DIR)
	docker run --rm -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build.sh
