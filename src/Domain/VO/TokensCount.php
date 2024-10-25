<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class TokensCount implements \JsonSerializable
{

    public function __construct(private int $tokens, private int $characters)
    {

    }

    public function getTokens(): int
    {
        return $this->tokens;
    }

    public function getCharacters(): int
    {
        return $this->characters;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}