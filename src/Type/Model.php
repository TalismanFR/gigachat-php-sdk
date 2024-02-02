<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat\Type;

class Model implements ArrayConverterInterface
{
    public const ID_GIGACHAT = 'GigaChat';
    public const ID_GIGACHAT_PRO = 'GigaChat-Pro';
    public const ID_GIGACHAT_PLUS = 'GigaChat-Plus';
    public const ID_GIGACHAT_LATEST = 'GigaChat:latest';

    private string $id;
    private string $object;
    private string $ownedBy;

    public function __construct(
        string $id,
        string $object,
        string $ownedBy
    )
    {
        $this->id = $id;
        $this->object = $object;
        $this->ownedBy = $ownedBy;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getOwnedBy(): string
    {
        return $this->ownedBy;
    }

    public static function createFromArray(array $array): self
    {
        return new Model(
            $array['id'],
            $array['object'],
            $array['owned_by']
        );
    }
}
