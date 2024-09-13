<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Contract;

use Ramsey\Uuid\UuidInterface;
use Talismanfr\GigaChat\Type\AccessToken;

interface GigaChatOAuthInterface
{
    public function getAccessToken(?UuidInterface $rqUID = null): AccessToken;
}