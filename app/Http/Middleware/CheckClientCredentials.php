<?php

namespace App\Http\Middleware;

use Closure;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laravel\Passport\Http\Middleware\CheckCredentials;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class CheckClientCredentials
{
    
    /**
     * The Resource Server instance.
     *
     * @var \League\OAuth2\Server\ResourceServer
     */
    protected $server;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        $psr = (new PsrHttpFactory(
            new ServerRequestFactory,
            new StreamFactory,
            new UploadedFileFactory,
            new ResponseFactory
        ))->createRequest($request);

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);
        } catch (OAuthServerException $e) {
            return response()->json($e->getPayload(), $e->getHttpStatusCode()); 
        }

        return $next($request);
    }

    protected function validateCredentials($token) {}
    protected function validateScopes($token, $scopes) {}
}
