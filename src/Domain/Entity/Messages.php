<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\Entity;

use Talismanfr\GigaChat\Domain\VO\Message;

final class Messages implements \JsonSerializable
{

    private array $messages = [];

    private int $maxOrder = -1;

    public function __construct(Message ...$messages)
    {
        $this->messages = $messages;
        $this->orderingMessage();
    }

    public function addMessage(Message $message): void
    {
        $this->maxOrder++;
        $this->messages[$this->maxOrder] = $message->withOrder($this->maxOrder);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getMaxIndex(): int
    {
        return $this->maxOrder;
    }

    public function jsonSerialize(): mixed
    {
        return $this->messages;
    }

    private function orderingMessage(): void
    {
        $messages = [];
        foreach ($this->messages as $message) {
            if ($this->maxOrder < $message->getIndex()) {
                $this->maxOrder = $message->getIndex();
            }
            $messages[$message->getIndex()] = $message;
        }

        ksort($messages, SORT_NUMERIC);
        $this->messages = $messages;
    }
}