<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat;

final class Url
{
    public const OAUTH_HOST = 'https://ngw.devices.sberbank.ru:9443/api';
    public const OAUTH_API_VERSION = 'v2';
    public const OAUTH_API_URL = self::OAUTH_HOST . '/' . self::OAUTH_API_VERSION . '/';
    public const GIGACHAT_HOST = 'https://gigachat.devices.sberbank.ru/api';
    public const GIGACHAT_API_VERSION = 'v1';
    public const GIGACHAT_API_URL = self::GIGACHAT_HOST . '/' . self::GIGACHAT_API_VERSION . '/';
}
