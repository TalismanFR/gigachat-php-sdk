<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Contract;

use Talismanfr\GigaChat\Domain\Entity\Dialog;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;

interface GigaChatServiceInterface
{
    public function models(): Models;

    public function completions(Dialog $dialog): CompletionResponse;
}