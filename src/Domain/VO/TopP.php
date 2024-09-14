<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class TopP implements \JsonSerializable
{
    public function __construct(
        private float $topP
    )
    {
        if ($this->topP < 0.0 || $this->topP > 1.0) {
            throw new \InvalidArgumentException('TopP must be greater than 0.0 and less than 1.0');
        }
    }

    public function getTopP(): float
    {
        return $this->topP;
    }

    public function jsonSerialize(): float
    {
        return $this->topP;
    }
}