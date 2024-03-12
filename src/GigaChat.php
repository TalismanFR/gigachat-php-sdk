<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat;

use Edvardpotter\GigaChat\Type\Completion;
use Edvardpotter\GigaChat\Type\Message;
use Edvardpotter\GigaChat\Type\Model;
use Edvardpotter\GigaChat\Type\TokensCount;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GigaChat
{
    private string $authToken;
    private ?ClientInterface $client;

    public function __construct(
        string           $authToken,
                         $cert,
        ?ClientInterface $client = null
    )
    {
        $this->authToken = $authToken;

        if ($client === null) {
            $this->client = new Client([
                'base_uri' => Url::GIGACHAT_API_URL,
                RequestOptions::VERIFY => $cert,
            ]);
        } else {
            $this->client = $client;
        }
    }

    /**
     * @return array<int, Model>
     * @throws \JsonException
     * @throws ClientExceptionInterface
     */
    public function getModels(): array
    {
        $response = $this->sendRequest(
            new Request(
                'GET',
                'models',
                [
                    'Authorization' => 'Bearer ' . $this->authToken,
                ]
            )
        );

        return array_map(function (array $model) {
            return Model::createFromArray($model);
        }, $this->json($response)['data']);
    }

    /**
     * @param Message[] $messages
     */
    public function chatCompletions(
        array  $messages = [],
        string $model = Model::ID_GIGACHAT_LATEST,
        float  $temperature = 1.0,
        float  $topP = 0.1,
        int    $n = 1,
        bool   $stream = false,
        int    $maxTokens = 1024,
        float  $repetitionPenalty = 1,
        int    $updateInterval = 0
    ): Completion
    {
        $response = $this->sendRequest(
            new Request(
                'POST',
                'chat/completions',
                [
                    'Authorization' => 'Bearer ' . $this->authToken,
                ],
                json_encode([
                    'model' => $model,
                    'messages' => array_map(function (Message $message) {
                        return $message->toArray();
                    }, $messages),
                    'temperature' => $temperature,
                    'top_p' => $topP,
                    'n' => $n,
                    'stream' => $stream,
                    'max_tokens' => $maxTokens,
                    'repetition_penalty' => $repetitionPenalty,
                    'update_interval' => $updateInterval,
                ]),
            )
        );

        return Completion::createFromArray($this->json($response));
    }

    public function getFile(string $fileId): StreamInterface
    {
        $response = $this->sendRequest(
            new Request(
                'GET',
                'files/' . $fileId . '/content',
                [
                    'Authorization' => 'Bearer ' . $this->authToken,
                ]
            )
        );

        return $response->getBody();
    }

    /**
     * @param string[] $input
     */
    public function getEmbeddings(
        array  $input,
        string $model = 'Embeddings'
    ): array
    {
        $response = $this->sendRequest(
            new Request(
                'POST',
                'embeddings',
                [
                    'Authorization' => 'Bearer ' . $this->authToken,
                ],
                json_encode([
                    'input' => $input,
                    'model' => $model,
                ]),
            )
        );

        return $this->json($response);
    }

    public function tokensCount(
        string $model,
        string $input
    ): TokensCount
    {
        $response = $this->sendRequest(
            new Request(
                'POST',
                'tokens/count',
                [
                    'Authorization' => 'Bearer ' . $this->authToken,
                ],
                json_encode([
                    'model' => $model,
                    'input' => $input,
                ]),
            )
        );

        return TokensCount::createFromArray($this->json($response));
    }

    private function json(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \RuntimeException
     */
    protected function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->client->sendRequest($request);
        }
        catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }
        if ($response->getStatusCode() !== 200) {
            $message = 'HTTP Status Code: ' . $response->getStatusCode() . '. Response Body: ' . $response->getBody();
            throw new \RuntimeException($message);
        }

        return $response;
    }
}
