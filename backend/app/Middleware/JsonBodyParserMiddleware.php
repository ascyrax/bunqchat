<?php

// app/Middleware/JsonBodyParserMiddleware.php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        error_log(var_export($parsedBody, true));
        $rawBody = $request->getBody()->getContents();

        // Reset the stream for further processing
        $request = $request->withBody(new \Slim\Psr7\Stream(fopen('php://temp', 'r+')));
        $request->getBody()->write($rawBody); // Write raw body back to the stream

        $errorResponse = [];

        if ($parsedBody === null) {
            error_log('Parsed body is null. Raw body contents: ' . $rawBody);
            $errorResponse = [
                'success' => false,
                'message' => 'Empty request.'
            ];
        } else {
            error_log('Parsed body: ' . var_export($parsedBody, true));
        }

        $groupName = $parsedBody['group_name'] ?? null;

        if (is_null($groupName)) {
            $errorResponse = [
                'success' => false,
                'message' => 'group_name missing.'
            ];
        } else if ($groupName) {
            $request = $request->withAttribute('group_name', $groupName);
        }
        error_log(var_export($groupName, true));
        
        $response = $handler->handle($request);

        if (!empty($errorResponse)) {
            $response
                ->withStatus(400) // 400 => bad request
                ->withHeader('Content-Type', 'application/json')
                ->getBody()
                ->write(var_export($errorResponse, true));
            return $response;
        }
        return $response;
    }
}
