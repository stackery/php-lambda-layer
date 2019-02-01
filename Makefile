ROOT_DIR:=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

php71.zip:
	docker run --rm -e http_proxy=${http_proxy} -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build.sh

php72.zip:
	docker run --rm -e http_proxy=${http_proxy} -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build-php-remi.sh 2

php73.zip:
	docker run --rm -e http_proxy=${http_proxy} -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build-php-remi.sh 3

upload: php71.zip
	./upload.sh

publish: php71.zip
	./publish.sh

clean:
	rm -f php71.zip php72.zip php73.zip

