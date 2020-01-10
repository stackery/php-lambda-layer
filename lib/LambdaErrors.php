<?php

namespace LambdaErrors;

class InvocationError extends \Exception
{
}

class LambdaError extends \Exception
{
    protected $errorClass;
    protected $errorType;
    protected $errorMessage;
    protected $stackTrace;

    public function __construct(\Throwable $originalError, $classification = 'Function')
    {
        $this->errorClass = get_class($originalError);
        $this->errorType = "{$classification}<{$this->errorClass}>";
        $file = $originalError->getFile();
        $line = $originalError->getLine();
        $message = $originalError->getMessage();
        $this->errorMessage = "{$file}({$line}): {$message}";
        $this->stackTrace = $this->_sanitize_stacktrace($originalError->getTraceAsString());
        parent::__construct($this->errorMessage);
    }

    public function toLambdaResponse()
    {
        return [
            'errorMessage' => $this->errorMessage,
            'errorType' => $this->errorType,
            'stackTrace' => $this->stackTrace,
        ];
    }

    public function runtimeErrorType()
    {
        $classification = 'Function<UserException>';
        if ($this->_allowedError()) {
            $classification = $this->errorType;
        }
        return $classification;
    }

    private function _sanitize_stacktrace($stacktrace)
    {
        $ret = [];
        $safeTrace = true;
        foreach (array_slice(explode(PHP_EOL, $stacktrace), 0, 100) as $trace) {
            if ($safeTrace) {
                [$no, $file] = explode(' ', $trace);
                if (preg_match('@^/opt/lib/@', $file) === 1) {
                    $safeTrace = false;
                } else {
                    $ret[] = $trace;
                }
            }
        }
        return $ret;
    }

    private function _allowedError()
    {
        return $this->_standardError();
    }

    private function _standardError()
    {
        return true; // @Todo: To determine standard exception classes.
    }
}

class LambdaHandlerError extends LambdaError
{
}

class LambdaHandlerCriticalException extends LambdaError
{
}

class LambdaRuntimeError extends LambdaError
{
    public function __construct($originalError)
    {
        parent::__construct($originalError, 'Runtime');
    }
}

class LambdaRuntimeInitError extends LambdaError
{
    public function __construct($originalError)
    {
        parent::__construct($originalError, 'Init');
    }
}
