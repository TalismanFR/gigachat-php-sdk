<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat\Type;

class TokensCount implements ArrayConverterInterface
{
    private string $object;
    private int $tokens;
    private int $characters;

    public function __construct(
        string $object,
        int    $tokens,
        int    $characters
    )
    {
        $this->object = $object;
        $this->tokens = $tokens;
        $this->characters = $characters;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getTokens(): int
    {
        return $this->tokens;
    }

    public function getCharacters(): int
    {
        return $this->characters;
    }

    public static function createFromArray(array $array): self
    {
        return new TokensCount(
            $array['object'],
            $array['tokens'],
            $array['characters']
        );
    }
}
