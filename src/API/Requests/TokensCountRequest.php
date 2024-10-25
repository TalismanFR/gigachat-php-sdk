<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Requests;

use Talismanfr\GigaChat\Domain\VO\Model;

class TokensCountRequest implements \JsonSerializable
{
    public function __construct(
        readonly Model $model,
        readonly array $input
    )
    {
        if (count($this->input) === 0) {
            throw new \InvalidArgumentException('input cannot be empty');
        }
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}