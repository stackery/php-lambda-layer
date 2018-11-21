ROOT_DIR:=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

php71.zip:
	docker run --rm -v $(ROOT_DIR):/opt/layer lambci/lambda:build-nodejs8.10 /opt/layer/build.sh

upload: php71.zip
	./upload.sh

publish: php71.zip
	./publish.sh

clean:
	rm php71.zip
