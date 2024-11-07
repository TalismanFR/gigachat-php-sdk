<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\Entity;

use Psr\EventDispatcher\EventDispatcherInterface;
use Talismanfr\GigaChat\Domain\Event\FunctionCallEvent;
use Talismanfr\GigaChat\Domain\VO\FunctionCall;
use Talismanfr\GigaChat\Domain\VO\FunctionModel;
use Talismanfr\GigaChat\Domain\VO\Message;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\TopP;
use Talismanfr\GigaChat\Domain\VO\UsageTokens;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;

final class Dialog implements \JsonSerializable
{
    private ?UsageTokens $usage = null;

    public function __construct(
        private Model                     $model,
        private Messages                  $messages,
        private float                     $temperature = 0.2,
        private TopP                      $topP = new TopP(0.1),
        private int                       $maxTokens = 1024,
        private float                     $repetitionPenalty = 1,
        private ?FunctionCall             $functionCall = null,
        private ?Functions                $functions = null,
        private ?EventDispatcherInterface $eventDispatcher = null
    )
    {
        if ($this->maxTokens <= 0) {
            throw new \InvalidArgumentException('Max tokens must be greater than 0');
        }
        if ($this->temperature < 0) {
            throw new \InvalidArgumentException('Temperature must be greater than 0');
        }
    }

    public function processedCompletionResponse(CompletionResponse $response): void
    {
        foreach ($response->choices as $choice) {
            $newMessage = new Message(0, $choice->message->content, $choice->message->role, $choice->message->functions_state_id, $choice->message->function_call);
            $this->messages->addMessage($newMessage);

            $this->usage = $response->usage;

            //send event function_call if EventDispatcher exist
            if (isset($this->eventDispatcher) && $choice->message->function_call) {
                if ($lastMessage = $this->getMessages()->getLastMessage()) {
                    $this->eventDispatcher->dispatch(new FunctionCallEvent($this, $lastMessage));
                }
            }
        }
    }

    public function addFunction(FunctionModel $functionModel): void
    {
        if (!$this->functionCall) {
            $this->functionCall = FunctionCall::auto();
        }
        if (!$this->functions) {
            $this->functions = new Functions();
        }
        $this->functions->addFunction($functionModel);
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addMessage(Message $message): void
    {
        $this->messages->addMessage($message);
    }

    public function getUsage(): ?UsageTokens
    {
        return $this->usage;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getMessages(): Messages
    {
        return $this->messages;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getTopP(): TopP
    {
        return $this->topP;
    }

    public function getMaxTokens(): int
    {
        return $this->maxTokens;
    }

    public function getRepetitionPenalty(): float
    {
        return $this->repetitionPenalty;
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getFunctionCall(): ?FunctionCall
    {
        return $this->functionCall;
    }

    public function getFunctions(): ?Functions
    {
        return $this->functions;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'model' => $this->model,
            'messages' => $this->messages,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'top_p' => $this->topP,
            'repetition_penalty' => $this->repetitionPenalty,
            'function_call' => $this->functionCall,
            'functions' => $this->functions
        ];
    }
}