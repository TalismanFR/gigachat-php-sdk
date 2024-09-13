<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service;

use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\Domain\Entity\Dialog;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Exception\ErrorGetModelsExeption;
use Talismanfr\GigaChat\Mapper\GigaChatMapper;
use Talismanfr\GigaChat\Service\Contract\GigaChatServiceInterface;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;

class GigaChatService implements GigaChatServiceInterface
{
    public function __construct(
        private GigaChatApiInterface $api,
        private GigaChatMapper       $mapper
    )
    {

    }

    public function models(): Models
    {
        $response = $this->api->models();
        if ($response->getStatusCode() !== 200) {
            throw  new ErrorGetModelsExeption($response, 'Error get models', $response->getStatusCode());
        }
        return $this->mapper->modelsFromResponse($response);
    }

    public function completions(Dialog $dialog): CompletionResponse
    {
        $response = $this->api->completions($dialog);
        $completion = $this->mapper->completionFromResponse($response);
        $dialog->processedCompletionResponse($completion);

        return $completion;
    }
}