<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\Event;

use Talismanfr\GigaChat\Domain\Entity\Dialog;
use Talismanfr\GigaChat\Domain\VO\Message;

class FunctionCallEvent
{
    public function __construct(private Dialog $dialog, private Message $messageFunctionCall)
    {

    }

    public function getDialog(): Dialog
    {
        return $this->dialog;
    }

    public function getMessageFunctionCall(): Message
    {
        return $this->messageFunctionCall;
    }

}