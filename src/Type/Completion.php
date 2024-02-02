<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat\Type;

class Completion implements ArrayConverterInterface
{
    private \DateTimeImmutable $created;
    private string $model;
    private string $object;
    private Usage $usage;
    private array $choices;

    /**
     * @param Choice[] $choices
     */
    public function __construct(
        \DateTimeImmutable $created,
        string             $model,
        string             $object,
        Usage              $usage,
        array              $choices
    )
    {
        $this->created = $created;
        $this->model = $model;
        $this->object = $object;
        $this->usage = $usage;
        $this->choices = $choices;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getUsage(): Usage
    {
        return $this->usage;
    }

    /**
     * @return Choice[]
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    public static function createFromArray(array $array): self
    {
        return new Completion(
            new \DateTimeImmutable('@' . $array['created']),
            $array['model'],
            $array['object'],
            Usage::createFromArray($array['usage']),
            array_map(function (array $choice) {
                return Choice::createFromArray($choice);
            }, $array['choices'])
        );
    }
}
