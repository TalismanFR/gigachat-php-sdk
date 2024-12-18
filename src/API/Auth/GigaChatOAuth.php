<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\UuidInterface;
use Talismanfr\GigaChat\API\Auth\VO\AccessToken;
use Talismanfr\GigaChat\API\Contract\GigaChatOAuthInterface;
use Talismanfr\GigaChat\API\Contract\UrlsInterface;
use Talismanfr\GigaChat\API\Urls;
use Talismanfr\GigaChat\Domain\VO\Scope;
use Talismanfr\GigaChat\Exception\ErrorGetAccessTokenException;

final class GigaChatOAuth implements GigaChatOAuthInterface
{
    private string $clientId;
    private string $clientSecret;
    private Scope $scope;
    private Client|ClientInterface $client;

    public function __construct(
        string                         $clientId,
        string                         $clientSecret,
        Scope                          $scope = Scope::GIGACHAT_API_PERS,
        ?ClientInterface               $client = null,
        private readonly UrlsInterface $urls = new Urls()
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;

        if ($client === null) {
            $this->client = new Client([
                'base_uri' => $this->urls->getOAuthUrl(),
                RequestOptions::VERIFY => false,
                RequestOptions::HTTP_ERRORS => false
            ]);
        } else {
            $this->client = $client;
        }
    }

    /**
     * @throws ErrorGetAccessTokenException
     * @throws \JsonException
     */
    public function getAccessToken(?UuidInterface $rqUID = null): AccessToken
    {
        if ($rqUID === null) {
            $rqUID = \Ramsey\Uuid\Uuid::uuid4();
        }

        /** @psalm-suppress UndefinedInterfaceMethod */
        $response = $this->client->send(
            new Request(
                'POST',
                'oauth',
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'RqUID' => $rqUID->toString(),
                ],
                http_build_query(
                    [
                        'scope' => $this->scope->value,
                    ],
                    '',
                    '&'
                ),
            )
        );

        if ($response->getStatusCode() !== 200) {
            throw new ErrorGetAccessTokenException($response, 'Error while getting access token', $response->getStatusCode());
        }

        $data = $this->json($response);
        $expireAtDatetime = new \DateTimeImmutable('@' . floor($data['expires_at'] / 1000));

        return new AccessToken(
            $data['access_token'],
            $expireAtDatetime
        );
    }

    private function json(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }
}
