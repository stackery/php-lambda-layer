# PHP Layer For AWS Lambda

Ever wanted to run PHP websites in AWS Lambda? It's your lucky day! This Lambda Runtime Layer runs the [PHP 7.1 webserver](http://php.net/manual/en/features.commandline.webserver.php) in response to [AWS API Gateway](https://aws.amazon.com/api-gateway/) requests.

**:warning: This is an experimental AWS Lambda Runtime Layer. It should not be used for production services. Please read the disclaimer at the bottom of this readme before using!**

## Current Layer Version ARN
When creating/updating a Lambda function you must specify  a specific version of the layer. This readme will be kept up to date with the latest version available. The latest available Lambda Layer Version ARN is:

**arn:aws:lambda:<region>:887080169480:layer:php71:2**

### Usage
#### General Usage
The layer runs the PHP 7.1 [PHP webserver](http://php.net/manual/en/features.commandline.webserver.php) in /var/task, the root directory of function code packages:

```sh
$ php -S localhost:8000 '<handler>'
```

The Lambda Function Handler property specifies the location of the of the webserver router script. This script must be provided.

##### Extensions
Extensions can be built using the lambci/lambda:build-nodejs8.10 Docker image. It is recommended that extensions be built into a Lambda Layer in /lib/php/7.1/modules. Then, in a php.ini file in the Lambda Function root directory put:

```ini
extension_dir=/opt/lib/php/7.1/modules
extension=...
```

The following extensions are built into the layer and available in /opt/lib/php/7.1/modules:

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

#### Example
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
      CodeUri: src/server
      Runtime: provided
      Handler: router.php
      MemorySize: 3008
      Timeout: 30
      Tracing: Active
      Layers:
        - !Sub arn:aws:lambda:${AWS::Region}:887080169480:layer:php71:2
      Events:
        api:
          Type: Api
          Properties:
            Path: /{proxy+}
            Method: ANY
```

Now create the PHP application in `src/server`. We're going to create a webserver router script first. Put the following in `router.php`:

```php
<?php
if (is_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SERVER['SCRIPT_NAME'])) {
  // Serve static files directly
  return false;
}

// Run index.php for all requests
$_SERVER['SCRIPT_NAME'] = '/index.php';
require 'index.php';
?>
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
        ├── index.php
        └── router.php
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
Build the layer by:

1. Installing a Docker environment
1. Running `make`

This will launch a Docker container that will build php71.zip.

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