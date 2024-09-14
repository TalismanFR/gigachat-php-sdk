<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class FunctionProperties implements \JsonSerializable
{

    private array $functionProperties;

    private array $required = [];

    public function __construct(
        FunctionProperty ...$functionProperties
    )
    {
        $this->functionProperties = $functionProperties;
        foreach ($functionProperties as $functionProperty) {
            if ($functionProperty->isRequired()) {
                $this->required[] = $functionProperty->getName();
            }
        }
    }

    public function getFunctionProperties(): array
    {
        return $this->functionProperties;
    }

    public function getRequired(): ?array
    {
        return empty($this->required) ? null : $this->required;
    }

    public function jsonSerialize(): mixed
    {
        $result = [];
        foreach ($this->functionProperties as $functionProperty) {
            $result[$functionProperty->getName()] = [
                'type' => $functionProperty->getType(),
                'description' => $functionProperty->getDescription(),
            ];
            if ($functionProperty->getEnum()) {
                $result[$functionProperty->getName()]['enum'] = $functionProperty->getEnum();
            }
            if ($functionProperty->getItems()) {
                $result[$functionProperty->getName()]['items'] = $functionProperty->getItems();
            }
        }

        return $result;
    }
}