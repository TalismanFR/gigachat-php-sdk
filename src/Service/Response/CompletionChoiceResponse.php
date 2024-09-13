<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Response;

use Talismanfr\GigaChat\Domain\VO\FinishReason;

final class CompletionChoiceResponse implements \JsonSerializable
{

    public function __construct(
        readonly CompletionMessageResponse $message,
        readonly int                       $index,
        readonly FinishReason              $finish_reason,
    )
    {

    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}