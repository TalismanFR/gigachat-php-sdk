<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class Message implements \JsonSerializable
{

    public function __construct(
        private int     $index,
        private string  $content,
        private Role    $role = Role::USER,
        private ?string $functionsStateId = null
    )
    {

    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFunctionsStateId(): ?string
    {
        return $this->functionsStateId;
    }

    public function withOrder(int $order): self
    {
        $message = clone $this;
        $message->index = $order;
        return $message;
    }

    public function jsonSerialize(): mixed
    {
        //todo function_call
        return [
            'role' => $this->role->value,
            'content' => $this->content,
            'function_state_id' => $this->functionsStateId
        ];
    }
}