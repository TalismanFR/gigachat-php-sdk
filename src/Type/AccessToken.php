<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Type;

class AccessToken implements ArrayConverterInterface
{
    private string $accessToken;
    private \DateTimeImmutable $expiresAt;

    public function __construct(
        string             $accessToken,
        \DateTimeImmutable $expiresAt
    )
    {
        $this->accessToken = $accessToken;
        $this->expiresAt = $expiresAt;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return new \DateTime() >= $this->expiresAt;
    }

    public static function createFromArray(array $array): self
    {
        $expireAtDatetime = new \DateTimeImmutable('@' . floor($array['expires_at'] / 1000));

        return new AccessToken(
            $array['access_token'],
            $expireAtDatetime
        );
    }
}
