<?php

class LambdaContext
{
    private $deadlineMs;
    private $awsRequestd;
    private $invokedFunctionArn;
    private $logGroupName;
    private $logStreamName;
    private $functionName;
    private $memoryLimitInMb;
    private $functionVersion;
    private $identity;
    private $clientContext;

    public function __get($name)
    {
        return $this->$name;
    }

    public function __construct($request)
    {
        $this->deadlineMs = (int)$request['Lambda-Runtime-Deadline-Ms'];
        $this->awsRequestId = $request['Lambda-Runtime-Aws-Request-Id'];
        $this->invokedFunctionArn = $request['Lambda-Runtime-Invoked-Function-Arn'];
        $this->logGroupName = getenv('AWS_LAMBDA_LOG_GROUP_NAME');
        $this->logStreamName = getenv('AWS_LAMBDA_LOG_STREAM_NAME');
        $this->functionName = getenv("AWS_LAMBDA_FUNCTION_NAME");
        $this->memoryLimitInMb = getenv('AWS_LAMBDA_FUNCTION_MEMORY_SIZE');
        $this->functionVersion = getenv('AWS_LAMBDA_FUNCTION_VERSION');
        if (isset($request['Lambda-Runtime-Cognito-Identity'])) {
            $this->identity = json_decode($request['Lambda-Runtime-Cognito-Identity']);
        }
        if (isset($request['Lambda-Runtime-Client-Context'])) {
            $this->clientContext = json_decode($request['Lambda-Runtime-Client-Context']);
        }
    }
}
