<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Response;

final class FunctionCallResponse implements \JsonSerializable
{

    public function __construct(
        private string $name,
        private array  $arguments
    )
    {

    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}