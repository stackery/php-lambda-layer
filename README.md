# PHP Layer For AWS Lambda

Ever wanted to run PHP websites in AWS Lambda? It's your lucky day! This Lambda Runtime Layer runs the [PHP 7.3/7.1  webserver](http://php.net/manual/en/features.commandline.webserver.php) in response to [AWS API Gateway](https://aws.amazon.com/api-gateway/) or [AWS Application Load Balancer](https://aws.amazon.com/elasticloadbalancing/features/#Details_for_Elastic_Load_Balancing_Products) requests.

And, if you're looking for a great way to build serverless apps of all kinds, be sure to check out [Stackery](https://stackery.io)!

This is an early iteration of the PHP runtime Layer which is not yet ready for production. Please feel free to use this Layer to learn about the Lambda Layers feature and begin experimenting with PHP functions. We welcome feedback and stay tuned for the production-ready version coming soon.

## Current Layer Version ARN
When creating/updating a Lambda function you must specify  a specific version of the layer. This readme will be kept up to date with the latest version available. The latest available Lambda Layer Version ARNs for PHP 7.3 and 7.1 are:

**arn:aws:lambda:\<region\>:887080169480:layer:php73:2**

**arn:aws:lambda:\<region\>:887080169480:layer:php71:9**

See [Releases](https://github.com/stackery/php-lambda-layer/releases) for release notes.

### Usage
#### General Usage
The layer runs the PHP 7.* [PHP webserver](http://php.net/manual/en/features.commandline.webserver.php) in /var/task, the root directory of function code packages:

```sh
$ php -S localhost:8000 '<handler>'
```

The Lambda Function Handler property specifies the location of the the script executed in response to an incoming API Gateway or Application Load Balancer request.

#### Configuration Files
There are three locations where PHP configuration may be located:

* Files in layer code packages located under /etc/php-${PHP_VERSION}.d/
* Files in function code package located under /php-${PHP_VERSION}.d/
* php.ini located at the root of the function code package

Replace ${PHP_VERSION} with '7.3', or '7.1' according to your preferred runtime.

##### Extensions
The following extensions are built into the layer and available in /opt/lib/php/${PHP_VERSION}/modules:

```
bz2.so
calendar.so
ctype.so
curl.so
dom.so
exif.so
fileinfo.so
ftp.so
gettext.so
iconv.so
json.so
phar.so
posix.so
shmop.so
simplexml.so
sockets.so
sysvmsg.so
sysvsem.so
sysvshm.so
tokenizer.so
wddx.so
xml.so
xmlreader.so
xmlwriter.so
xsl.so
zip.so
```

These extensions are not loaded by default. You must add the extension to a php.ini file to use it:

```ini
extension=json.so
```

Extensions can be built using the lambci/lambda:build-nodejs8.10 Docker image. It is recommended that custom extensions be provided by a separate Lambda Layer with the extension .so files placed in /lib/php/${PHP_VERSION}/modules/ so they can be loaded alongside the built-in extensions listed above.

#### SAM Example
Let's create an AWS SAM PHP application. We suggest using [Stackery](https://stackery.io) to make this super simple. It automates all the scaffolding shown below. But you may also choose to roll your own application from scratch.

First, install [AWS SAM CLI](https://github.com/awslabs/aws-sam-cli). Make sure to create a SAM deployment bucket as shown in [Packaging your application](https://github.com/awslabs/aws-sam-cli/blob/develop/docs/deploying_serverless_applications.rst#packaging-your-application)

Next, create a basic SAM application:

```sh
$ mkdir my-php-app
$ cd my-php-app
```

Create a template.yaml file with the following SAM infrastructure:

```yaml
AWSTemplateFormatVersion: 2010-09-09
Description: My PHP Application
Transform: AWS::Serverless-2016-10-31
Resources:
  phpserver:
    Type: AWS::Serverless::Function
    Properties:
      FunctionName: !Sub ${AWS::StackName}-phpserver
      Description: PHP Webserver
      CodeUri: src/php
      Runtime: provided
      Handler: index.php
      MemorySize: 3008
      Timeout: 30
      Tracing: Active
      Layers:
        - !Sub arn:aws:lambda:${AWS::Region}:887080169480:layer:php73:2
      Events:
        api:
          Type: Api
          Properties:
            Path: /{proxy+}
            Method: ANY
```

Lastly, let's write our script. Put this in `index.php`:

```php
Hello World! You've reached <?php print($_SERVER['REQUEST_URI']); ?>

```

You should now have a directory structure like:

```
.
├── template.yaml
└── src
    └── php
        └── index.php
```

We're ready to deploy! Run the following commands:

```sh
$ sam package \
    --template-file template.yaml \
    --output-template-file serverless-output.yaml \
    --s3-bucket <your SAM deployment bucket created above>

$ sam deploy \
    --template-file serverless-output.yaml \
    --stack-name my-first-serverless-php-service \
    --capabilities CAPABILITY_IAM
```

### Development
Build the layers by:

1. Installing a Docker environment
1. Running `make`

This will launch Docker containers that will build php73.zip and php71.zip.

If you are behind a proxy server, just set the environment variable `http_proxy` before
invoking `make`, eg.:

```sh
	$ export http_proxy=http://myproxy.acme.com:8080
	$ make php73.zip
```

#### Debugging Layer Builds

Run:

```sh
	$ docker run --rm -it -v `pwd`:/opt/layer lambci/lambda:build-nodejs8.10 /bin/bash
```

If you are on Windows, run this instead:

```sh
	> docker run --rm -it -v %cd%:/opt/layer lambci/lambda:build-nodejs8.10 /bin/bash
```

then manually execute the commands in the build.sh file.

### Disclaimer

> THIS SOFTWARE IS PROVIDED BY THE PHP DEVELOPMENT TEAM ``AS IS'' AND
> ANY EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
> THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
> PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE PHP
> DEVELOPMENT TEAM OR ITS CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
> INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
> (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
> SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
> HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
> STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
> ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
> OF THE POSSIBILITY OF SUCH DAMAGE.
