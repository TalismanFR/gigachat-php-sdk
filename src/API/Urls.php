<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API;

use Talismanfr\GigaChat\API\Contract\UrlsInterface;

final class Urls implements UrlsInterface
{
    public function __construct(
        private string $oauthHost = 'https://ngw.devices.sberbank.ru:9443/api',
        private string $oauthApiVersion = 'v2',
        private string $gigachatHost = 'https://gigachat.devices.sberbank.ru/api',
        private string $gigachatApiVersion = 'v1',
    )
    {

    }

    public function getOAuthHost(): string
    {
        return $this->oauthHost;
    }

    public function getOAuthApiVersion(): string
    {
        return $this->oauthApiVersion;
    }

    public
    function getOAuthUrl(): string
    {
        return $this->oauthHost . '/' . $this->oauthApiVersion . '/';
    }

    public function getGigaChatHost(): string
    {
        return $this->gigachatHost;
    }

    public function getGigaChatApiVersion(): string
    {
        return $this->gigachatApiVersion;
    }

    public function getGigaChatApiUrl(): string
    {
        return $this->getGigaChatHost() . '/' . $this->gigachatApiVersion . '/';
    }
}