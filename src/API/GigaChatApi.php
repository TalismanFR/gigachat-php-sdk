<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\API\Contract\GigaChatOAuthInterface;
use Talismanfr\GigaChat\API\Requests\EmbeddingsRequest;
use Talismanfr\GigaChat\API\Requests\TokensCountRequest;
use Talismanfr\GigaChat\Domain\Entity\Dialog;
use Talismanfr\GigaChat\Url;

final class GigaChatApi implements GigaChatApiInterface
{
    private const string URL_MODELS = 'models';
    private const string URL_CHAT_COMPLETION = 'chat/completions';
    private const string URL_TOKENS_COUNT = 'tokens/count';
    private const string URL_EMBEDDINGS = 'embeddings';

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

    /**
     * @throws ClientExceptionInterface
     */
    public function completions(Dialog $dialog): ResponseInterface
    {
        return $this->client->sendRequest(
            new Request('POST', self::URL_CHAT_COMPLETION, [
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
                json_encode($dialog, JSON_UNESCAPED_UNICODE)
            )
        );
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function tokensCount(TokensCountRequest $request): ResponseInterface
    {
        return $this->client->sendRequest(
            new Request('POST', self::URL_TOKENS_COUNT, [
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
                json_encode($request, JSON_UNESCAPED_UNICODE)
            )
        );
    }

    public function embeddings(EmbeddingsRequest $request): ResponseInterface
    {
        return $this->client->sendRequest(
            new Request('POST', self::URL_EMBEDDINGS, [
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
                json_encode($request, JSON_UNESCAPED_UNICODE)
            )
        );
    }
}