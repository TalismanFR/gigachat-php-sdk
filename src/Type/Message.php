<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Type;

class Message implements ArrayConverterInterface
{
    public const ROLE_SYSTEM = 'system';
    public const ROLE_USER = 'user';
    public const ROLE_ASSISTANT = 'assistant';
    public const ROLE_SEARCH_RESULT = 'search_result';

    private string $content;
    private string $role;

    public function __construct(
        string $content,
        string $role = self::ROLE_USER
    )
    {
        $this->content = $content;
        $this->role = $role;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->content,
        ];
    }

    public static function createFromArray(array $array): self
    {
        return new Message(
            $array['content'],
            $array['role']
        );
    }
}
