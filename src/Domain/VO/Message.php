<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

use Ramsey\Uuid\UuidInterface;

final class Message implements \JsonSerializable
{

    /**
     * @param string|null $name User for role function
     * @param UuidInterface[]|string[] $attachments
     */
    public function __construct(
        private int           $index,
        private string        $content,
        private Role          $role = Role::USER,
        private ?string       $functionsStateId = null,
        private ?FunctionCall $functionCall = null,
        private ?string       $name = null,
        private ?array        $attachments = []
    )
    {

    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getFunctionCall(): ?FunctionCall
    {
        return $this->functionCall;
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

    /**
     * Если сообщение является вызовом функции (function_call), то данная функция сформирует сообщение ответа
     * @param string $content
     * @return self|null
     */
    public function buildFunctionResult(string $content): ?self
    {
        if (is_null($this->functionCall?->getName())) {
            return null;
        }

        return new Message(0, $content, Role::FUNCTION, null, null, $this->getFunctionCall()?->getName());
    }

    public function jsonSerialize(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content,
            'function_state_id' => $this->functionsStateId,
            'function_call' => $this->functionCall,
            'name' => $this->name,
            'attachments' => $this->attachments
        ];
    }
}