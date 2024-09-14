<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Support;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Talismanfr\GigaChat\Domain\Event\FunctionCallEvent;

class FunctionCallSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [FunctionCallEvent::class => 'functionCall'];
    }

    function functionCall(FunctionCallEvent $event)
    {
        $dialog = $event->getDialog();
        $function_name = $event->getMessageFunctionCall()->getFunctionCall()->getName();
        if ($function_name === 'player_number_name') {
            $response = $event->getMessageFunctionCall()->buildFunctionResult(json_encode(['player_number_name' => 'Иванов Иван Иванович']));
            $dialog->addMessage($response);
        }
    }
}