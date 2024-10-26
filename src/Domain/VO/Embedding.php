<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class Embedding implements \JsonSerializable
{
    /**
     * @param float[] $embedding
     */
    public function __construct(
        private int   $promtTokens,
        private array $embedding,
        private int   $index
    )
    {

    }

    public function getPromtTokens(): int
    {
        return $this->promtTokens;
    }

    /**
     * @return float[]
     */
    public function getEmbedding(): array
    {
        return $this->embedding;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}