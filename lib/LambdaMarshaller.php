<?php

class LambdaMarshaller
{
    # By default, JSON-parses the raw request body. This can be overwritten
    # by users who know what they are doing.
    public static function marshallRequest($rawRequest)
    {
        $contentType = $rawRequest->getHeader('Content-Type')[0];
        if ($contentType == 'application/json') {
            return json_decode($rawRequest->getBody()->getContents());
        } else {
            return $rawRequest->getBody()->getContents(); # return it unaltered
        }
    }

    # By default, just runs #to_json on the method's response value.
    # This can be overwritten by users who know what they are doing.
    # The response is an array of response, content-type.
    # If returned without a content-type, it is assumed to be application/json
    # Finally, StringIO/IO is used to signal a response that shouldn't be
    # formatted as JSON, and should get a different content-type header.
    public static function marshallResponse($methodResponse)
    {
        if (is_resource($methodResponse) && get_resource_type($methodResponse) == 'stream') {
            return [$methodResponse, 'application/unknown'];
        } else {
            return [json_encode($methodResponse, true), 'application/json'];
        }
    }
}
