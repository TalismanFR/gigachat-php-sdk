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
use Talismanfr\GigaChat\API\Contract\UrlsInterface;
use Talismanfr\GigaChat\API\Requests\EmbeddingsRequest;
use Talismanfr\GigaChat\API\Requests\LoadFileRequest;
use Talismanfr\GigaChat\API\Requests\TokensCountRequest;
use Talismanfr\GigaChat\Domain\Entity\Dialog;

final class GigaChatApi implements GigaChatApiInterface
{
    private const string URL_MODELS = 'models';
    private const string URL_CHAT_COMPLETION = 'chat/completions';
    private const string URL_TOKENS_COUNT = 'tokens/count';
    private const string URL_EMBEDDINGS = 'embeddings';
    private const string URL_FILES = 'files';

    private ClientInterface|Client $client;

    public function __construct(
        private readonly GigaChatOAuthInterface $auth,
        ?ClientInterface                        $client = null,
        private readonly UrlsInterface          $urls = new Urls()
    )
    {
        if (!$client) {
            $this->client = new Client([
                'base_uri' => $this->urls->getGigaChatApiUrl(),
                RequestOptions::VERIFY => false,
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]
            ]);
        } else {
            $this->client = $client;
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
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken(),
                'X-Session-ID' => $dialog->getSessionId()?->toString() ?? ''
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

    public function loadFile(LoadFileRequest $request): ResponseInterface
    {
        /** @psalm-suppress UndefinedInterfaceMethod */
        return $this->client->post(self::URL_FILES, [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
            RequestOptions::MULTIPART => [
                [
                    'name' => 'file',
                    'contents' => $request->getFile(),
                    'filename' => $request->getFilename()
                ],
                [
                    'name' => 'purpose',
                    'contents' => $request->getPurpose()->value
                ]
            ]
        ]);
    }

    public function fileInfo(string $fileId): ResponseInterface
    {
        return $this->client->sendRequest(
            new Request('GET', self::URL_FILES . '/' . $fileId, [
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
            )
        );
    }

    public function files(): ResponseInterface
    {
        return $this->client->sendRequest(
            new Request('GET', self::URL_FILES, [
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
            )
        );
    }

    public function downloadFile(string $fileId): ResponseInterface
    {
        return $this->client->sendRequest(
            new Request('GET', self::URL_FILES . '/' . $fileId . '/content', [
                'Accept' => 'image/jpg',
                'Authorization' => 'Bearer ' . $this->auth->getAccessToken(Uuid::uuid4())->getAccessToken()
            ],
            )
        );
    }
}