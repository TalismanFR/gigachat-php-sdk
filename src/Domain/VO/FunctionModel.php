<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class FunctionModel implements \JsonSerializable
{

    /**
     * @param string $name
     * @param FunctionParameters $parameters
     * @param string|null $description
     * @param FewShotExample[] $fewShotExamples
     */
    public function __construct(
        private string             $name,
        private FunctionParameters $parameters,
        private ?string            $description = null,
        private array              $fewShotExamples = []

    )
    {

    }

    public function jsonSerialize(): array
    {
        $result = [
            'name' => $this->name,
            'parameters' => $this->parameters,
            'description' => $this->description,
        ];
        if ($this->fewShotExamples) {
            $result['few_shot_examples'] = $this->fewShotExamples;
        }
        return $result;
    }
}