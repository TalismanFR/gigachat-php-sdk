<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat\Type;

class Choice implements ArrayConverterInterface
{
    private int $index;
    private string $finishReason;
    private Message $message;

    public function __construct(
        int     $index,
        string  $finishReason,
        Message $message
    )
    {
        $this->index = $index;
        $this->finishReason = $finishReason;
        $this->message = $message;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getFinishReason(): string
    {
        return $this->finishReason;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public static function createFromArray(array $array): self
    {
        return new Choice(
            $array['index'],
            $array['finish_reason'],
            Message::createFromArray($array['message'])
        );
    }
}
