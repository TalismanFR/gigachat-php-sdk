<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Response;

use Talismanfr\GigaChat\Domain\VO\UsageTokens;

final class CompletionResponse implements \JsonSerializable
{

    /**
     * @param CompletionChoiceResponse[] $choices
     */
    public function __construct(
        readonly array              $choices,
        readonly \DateTimeImmutable $created,
        readonly string             $model,
        readonly string             $object,
        readonly UsageTokens        $usage
    )
    {

    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}