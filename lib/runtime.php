<?php

error_reporting(E_ALL | E_STRICT);

require_once 'composer/vendor/autoload.php';

require_once 'LambdaErrors.php';
require_once 'LambdaServer.php';
require_once 'LambdaHandler.php';
require_once 'LambdaContext.php';
require_once 'LambdaLogger.php';
require_once 'LambdaMarshaller.php';

$envHandler = getenv('_HANDLER');
$lambdaServer = new LambdaServer();
$runtimeLoopActive = true;
$exitCode = 0;

try {
    $lambdaHandler = new LambdaHandler($envHandler);
    require_once "{$lambdaHandler->handlerFileName}.php";
} catch (Throwable $t) {
    $runtimeLoopActive = false;
    $exitCode = -4;
    $e = new LambdaErrors\LambdaRuntimeInitError($t);
    LambdaLogger::logError($e, "Init error when loading handler {$envHandler}");
    $lambdaServer->sendInitError($e);
}

while ($runtimeLoopActive) {
    try {
        $rawRequest = $lambdaServer->nextInvocation();
        $headers = array_map(function($val){return $val[0];}, $rawRequest->getHeaders());
        if (isset($headers['Lambda-Runtime-Trace-Id'])) {
            putenv('_X_AMZN_TRACE_ID=' . $headers['Lambda-Runtime-Trace-Id']);
        }
        $request = LambdaMarshaller::marshallRequest($rawRequest);
    } catch (LambdaErrors\InvocationError $e) {
        $runtimeLoopActive = false;
        throw $e;
    }

    try {
        $requestId = $headers['Lambda-Runtime-Aws-Request-Id'];
        $context = new LambdaContext($headers);
        [$handlerResponse, $contentType] = $lambdaHandler->callHandler($request, $context);
        $lambdaServer->sendResponse($requestId, $handlerResponse, $contentType);
    } catch (LambdaErrors\LambdaHandlerError $e) {
        LambdaLogger::logError($e, "Error raised from handler method");
        $lambdaServer->sendErrorResponse($requestId, $e);
    } catch (LambdaErrors\LambdaHandlerCriticalException $e) {
        LambdaLogger::logError($e, "Critical exception from handler");
        $lambdaServer->sendErrorResponse($requestId, $e);
        $runtimeLoopActive = false;
        $exitCode = -1;
    } catch (LambdaErrors\LambdaRuntimeError $e) {
        $lambdaServer->sendErrorResponse($requestId, $e);
        $runtimeLoopActive = false;
        $exitCode = -2;
    }
}

exit($exitCode);
