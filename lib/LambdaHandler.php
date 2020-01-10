<?php

class LambdaHandler
{
    private $handlerFileName;
    private $handlerMethodName;
    private $handlerClass;

    public function __get($name)
    {
        if (in_array($name, ['handlerFileName', 'handlerMethodName'])) {
            return $this->$name;
        } else {
            throw new Exception("Cannot access private property {$name}");
        }
    }

    public function __construct($envHandler)
    {
        $handlerSplit = explode('.', $envHandler);
        if (count($handlerSplit) == 2) {
            [$this->handlerFileName, $this->handlerMethodName] = $handlerSplit;
        } elseif (count($handlerSplit) == 3) {
            [$this->handlerFileName, $this->handlerClass, $this->handlerMethodName] = $handlerSplit;
        } else {
            throw new Exception("Invalid handler {$handlerSplit}, must be of form FILENAME.METHOD or FILENAME.CLASS.METHOD where FILENAME corresponds with an existing PHP source file FILENAME.php, CLASS is an optional module/class namespace and METHOD is a callable method. If using CLASS, METHOD must be a static method.");
        }
    }

    public function callHandler($request, $context)
    {
        try {
            if ($this->handlerClass) {
                $fun = "{$this->handlerClass}::{$this->handlerMethodName}";
            } else {
                $fun = $this->handlerMethodName;
            }
            $response = call_user_func($fun, $request, $context);
            return LambdaMarshaller::marshallResponse($response);
        } catch (Error $e) {
            throw new LambdaErrors\LambdaHandlerCriticalException($e);
        } catch (Exception $e) {
            throw new LambdaErrors\LambdaHandlerError($e);
        }
    }
}
