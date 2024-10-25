<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Contract;

use Psr\Http\Message\ResponseInterface;
use Talismanfr\GigaChat\API\Requests\TokensCountRequest;
use Talismanfr\GigaChat\Domain\Entity\Dialog;

interface GigaChatApiInterface
{
    public function models(): ResponseInterface;

    public function completions(Dialog $dialog): ResponseInterface;

    public function tokensCount(TokensCountRequest $request): ResponseInterface;
}