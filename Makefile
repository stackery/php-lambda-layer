ROOT_DIR:=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

all: php71.zip php73.zip php71g.zip php73g.zip

php71.zip:
	docker run --rm -e http_proxy=${http_proxy} -v $(ROOT_DIR):/opt/layer lambci/lambda:build-provided /opt/layer/build.sh

php73.zip:
	docker run --rm -e http_proxy=${http_proxy} -v $(ROOT_DIR):/opt/layer lambci/lambda:build-provided /opt/layer/build-php-remi.sh 3

php71g.zip:
	docker run --rm -e GENERAL_EVENT=true -e http_proxy=${http_proxy} -v $(ROOT_DIR):/opt/layer lambci/lambda:build-provided /opt/layer/build.sh

php73g.zip:
	docker run --rm -e GENERAL_EVENT=true -e http_proxy=${http_proxy} -v $(ROOT_DIR):/opt/layer lambci/lambda:build-provided /opt/layer/build-php-remi.sh 3

upload71: php71.zip
	./upload.sh 7.1

upload73: php73.zip
	./upload.sh 7.3

upload71g: php71g.zip
	./upload.sh 7.1g

upload73g: php73g.zip
	./upload.sh 7.3g

publish71: php71.zip
	./publish.sh 7.1

publish73: php73.zip
	./publish.sh 7.3

publish71g: php71g.zip
	./publish.sh 7.1g

publish73g: php73g.zip
	./publish.sh 7.3g

clean:
	rm -f php71.zip php73.zip php71g.zip php73g.zip

