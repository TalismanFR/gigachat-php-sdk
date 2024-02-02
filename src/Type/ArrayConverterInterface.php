<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat\Type;

interface ArrayConverterInterface
{
    public static function createFromArray(array $array): self;
}
