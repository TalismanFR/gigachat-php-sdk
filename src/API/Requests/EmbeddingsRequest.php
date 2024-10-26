<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Requests;

use Talismanfr\GigaChat\Domain\VO\Model;

final readonly class EmbeddingsRequest implements \JsonSerializable
{

    public function __construct(
        public Model $model,
        public array $input
    )
    {

    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}