<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class Models implements \JsonSerializable
{
    private array $models;

    public function __construct(Model ...$models)
    {
        $this->models = $models;
    }

    public function getModels(): array
    {
        return $this->models;
    }

    public function jsonSerialize(): array
    {
        return $this->models;
    }
}