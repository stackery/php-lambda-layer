ROOT_DIR:=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

php71.zip:
	docker run --rm -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build.sh

php72.zip:
	docker run --rm -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build-php72.sh

php73.zip:
	docker run --rm -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build-php73.sh

upload: php71.zip
	./upload.sh

publish: php71.zip
	./publish.sh

clean:
	rm -f php71.zip php72.zip php73.zip

