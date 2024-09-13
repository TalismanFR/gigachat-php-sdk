<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Contract;

use Psr\Http\Message\ResponseInterface;

interface GigaChatApiInterface
{
    public function models(): ResponseInterface;
}