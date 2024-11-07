<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Response;

use Ramsey\Uuid\UuidInterface;
use Talismanfr\GigaChat\Domain\VO\Purpose;

final readonly class LoadFileResponse implements \JsonSerializable
{
    public function __construct(
        public UuidInterface      $id,
        public int                $bytes,
        public string             $accessPolicy,
        public \DateTimeImmutable $createdAt,
        public string             $filename,
        public Purpose            $purpose,

    )
    {
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}