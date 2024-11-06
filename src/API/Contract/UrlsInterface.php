<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Contract;

interface UrlsInterface
{
    public function getOAuthHost(): string;

    public function getOAuthApiVersion(): string;

    public function getOAuthUrl(): string;

    public function getGigaChatHost(): string;

    public function getGigaChatApiVersion(): string;

    public function getGigaChatApiUrl(): string;
}