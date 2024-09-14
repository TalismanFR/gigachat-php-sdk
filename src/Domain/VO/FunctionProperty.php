<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class FunctionProperty implements \JsonSerializable
{

    /**
     * @param string $name
     * @param string $type string, integer, enum, array
     * @param string $description
     * @param array|null $enum ['first','two']
     * @param array|null $items only for type=array ['type'=>'string']
     */
    public function __construct(
        private string $name,
        private string $type,
        private string $description,
        private bool   $required,
        private ?array $enum = null,
        private ?array $items = null,
    )
    {

    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEnum(): ?array
    {
        return $this->enum;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}