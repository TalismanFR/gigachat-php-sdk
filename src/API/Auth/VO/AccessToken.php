<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Auth\VO;

class AccessToken
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
}