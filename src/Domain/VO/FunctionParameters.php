<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;


final class FunctionParameters implements \JsonSerializable
{

    private string $type = 'object';

    public function __construct(
        private FunctionProperties $properties,
    )
    {

    }

    public function getProperties(): FunctionProperties
    {
        return $this->properties;
    }


    public function jsonSerialize(): mixed
    {
        $result = [
            'properties' => $this->properties,
            'type' => $this->type
        ];
        if (!is_null($this->properties->getRequired()) && count($this->properties->getRequired() ?? []) > 0) {
            $result['required'] = $this->properties->getRequired();
        }
        return $result;
    }
}