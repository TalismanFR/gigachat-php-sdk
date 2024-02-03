<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat;

use Edvardpotter\GigaChat\Type\Message;
use Edvardpotter\GigaChat\Type\Model;

class GigaChatDialog
{
    private GigaChat $gigaChat;
    /**
     * @var Message[]
     */
    private array $messages;

    public function __construct(GigaChat $gigaChat)
    {
        $this->gigaChat = $gigaChat;
    }

    public function getAnswer(
        Message  $message,
        string $model = Model::ID_GIGACHAT_LATEST,
        float  $temperature = 1.0,
        float  $topP = 0.1,
        int    $n = 1,
        bool   $stream = false,
        int    $maxTokens = 1024,
        float  $repetitionPenalty = 1,
        int    $updateInterval = 0
    ): Message
    {
        $this->messages[] = $message;

        $completion = $this->gigaChat->chatCompletions(
            $this->messages,
            $model,
            $temperature,
            $topP,
            $n,
            $stream,
            $maxTokens,
            $repetitionPenalty,
            $updateInterval
        );

        $message = $completion->getChoices()[0]->getMessage();
        $this->messages[] = $message;

        return $message;
    }

    public function reset(): void
    {
        $this->messages = [];
    }

    /**
     * @return Message[]
     */
    public function getHistory(): array
    {
        return $this->messages;
    }
}
