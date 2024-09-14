<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class FunctionCall implements \JsonSerializable
{

    public function __construct(
        private ?string $name,
        private array   $arguments = [],
    )
    {

    }

    public static function auto(): self
    {
        return new self('auto');
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function jsonSerialize(): null|string|array
    {
        return !$this->name || strtolower($this->name) === 'auto' ? $this->name : get_object_vars($this);
    }
}