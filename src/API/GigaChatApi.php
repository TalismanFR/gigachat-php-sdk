<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\API\Contract\GigaChatOAuthInterface;
use Talismanfr\GigaChat\Url;

final class GigaChatApi implements GigaChatApiInterface
{
    private const URL_MODELS = 'models';

    public function __construct(
        private GigaChatOAuthInterface $auth,
        private ?ClientInterface       $client = null
    )
    {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri' => Url::GIGACHAT_API_URL,
                RequestOptions::VERIFY => false,
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);
        }
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function models(): ResponseInterface
    {
        return $this->client->sendRequest(
            new Request('GET', self::URL_MODELS, [
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
            )
        );
    }
}