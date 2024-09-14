<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\Entity;

use Talismanfr\GigaChat\Domain\VO\FunctionModel;

final class Functions implements \JsonSerializable
{
    private array $functions = [];

    public function __construct(FunctionModel ...$functionsModel)
    {
        $this->functions = $functionsModel;
    }

    public function getFunctions(): array
    {
        return $this->functions;
    }

    public function addFunction(FunctionModel $function): void
    {
        $this->functions[] = $function;
    }

    public function jsonSerialize(): array
    {
        return $this->functions;
    }
}