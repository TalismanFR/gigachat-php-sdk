<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Support;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TestClient implements ClientInterface
{

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return new Response(200,$request->getHeaders(), $request->getBody());
    }


}