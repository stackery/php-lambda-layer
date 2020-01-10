<?php

class LambdaLogger
{
    const STDERR = 'php://stderr';

    public static function logError(LambdaErrors\LambdaError $error, $message)
    {
        if (isset($message)) {
            file_put_contents(self::STDERR, $message);
        }
        file_put_contents(self::STDERR, json_encode($error->toLambdaResponse(), JSON_PRETTY_PRINT));
    }
}
