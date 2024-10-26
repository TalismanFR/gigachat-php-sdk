<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;


final class Model implements \JsonSerializable
{
    public const string ID_GIGACHAT = 'GigaChat';
    public const string ID_GIGACHAT_PRO = 'GigaChat-Pro';
    public const string ID_GIGACHAT_PLUS = 'GigaChat-Plus';
    public const string ID_EMBEDDINGS = 'Embeddings';

    private string $id;
    private string $object;
    private string $ownedBy;

    private const DEFAULT_OWNED = 'salutedevices';

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

    public static function createGigaChat(): self
    {
        return new self(self::ID_GIGACHAT, 'model', self::DEFAULT_OWNED);
    }

    public static function createEmbeddings(): self
    {
        return new self(self::ID_EMBEDDINGS, 'model', self::DEFAULT_OWNED);
    }

    public static function createGigaChatPro(): self
    {
        return new self(self::ID_GIGACHAT_PRO, 'model', self::DEFAULT_OWNED);
    }

    public static function createGigaChatPlus(): self
    {
        return new self(self::ID_GIGACHAT_PLUS, 'model', self::DEFAULT_OWNED);
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

    public function jsonSerialize(): string
    {
        return $this->id;
    }
}
