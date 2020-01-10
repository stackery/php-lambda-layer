<?php

class LambdaServer
{
    const LONG_TIMEOUT = 1000000;
    private $http;

    public function __construct($serverAddress = null)
    {
        if (is_null($serverAddress)) {
            $serverAddress = 'http://' . getenv('AWS_LAMBDA_RUNTIME_API');
        }
        $this->http = new GuzzleHttp\Client([
            'base_uri' => $serverAddress,
        ]);
    }

    public function nextInvocation()
    {
        $path = "/2018-06-01/runtime/invocation/next";
        try {
            $response = $this->http->request('GET', $path, [
                'timeout' => self::LONG_TIMEOUT,
            ]);
            $status = $response->getStatusCode();
            if ($status == 200) {
                return $response;
            } else {
                throw new Exception("Received {$status} when waiting for next invocation.");
            }
        } catch (Exception $e) {
            throw new LambdaErrors\InvocationError($e);
        }
    }

    public function sendResponse($requestId, $responseObject, $contentType = 'application/json')
    {
        $path = "/2018-06-01/runtime/invocation/{$requestId}/response";
        try {
            if ($contentType == 'application/unkown') {
                $responseObject = stream_get_contents($responseObject);
            }
            $this->http->request('POST', $path, [
                'body' => $responseObject,
                'headers' => [
                    'Content-Type' => $contentType,
                ],
            ]);
        } catch (Exception $e) {
            throw new LambdaErrors\LambdaRuntimeError($e);
        }
    }

    public function sendErrorResponse($requestId, $error)
    {
        $path = "/2018-06-01/runtime/invocation/{$requestId}/error";
        try {
            $this->http->request('POST', $path, [
                'body' => json_encode($error->toLambdaResponse(), true),
                'headers' => [
                    'Lambda-Runtime-Function-Error-Type' => $error->runtimeErrorType(),
                ],
            ]);
        } catch (Exception $e) {
            throw new LambdaErrors\LambdaRuntimeError($e);
        }
    }

    public function sendInitError($error)
    {
        $path = '/2018-06-01/runtime/init/error';
        try {
            $this->http->request('POST', $path, [
                'body' => json_encode($error->toLambdaResponse(), true),
                'headers' => [
                    'Lambda-Runtime-Function-Error-Type' => $error->runtimeErrorType(),
                ],
            ]);
        } catch (Exception $e) {
            throw new LambdaErrors\LambdaRuntimeInitError($e);
        }
    }
}
