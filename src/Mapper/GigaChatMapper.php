<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Mapper;

use Psr\Http\Message\ResponseInterface;
use Talismanfr\GigaChat\Domain\VO\FinishReason;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Domain\VO\Role;
use Talismanfr\GigaChat\Domain\VO\UsageTokens;
use Talismanfr\GigaChat\Service\Response\CompletionChoiceResponse;
use Talismanfr\GigaChat\Service\Response\CompletionMessageResponse;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;

class GigaChatMapper
{
    public function modelsFromResponse(ResponseInterface $response): Models
    {
        $data = json_decode($response->getBody()->__toString(), true);
        $models = [];
        foreach ($data['data'] ?? [] as $item) {
            $models[] = Model::createFromArray($item);
        }

        return new Models(...$models);
    }

    public function completionFromResponse(ResponseInterface $response): CompletionResponse
    {
        $data = json_decode($response->getBody()->__toString(), true);
        $choices = [];
        foreach ($data['choices'] ?? [] as $choice) {
            //todo function call mapping
            $choices[] = new CompletionChoiceResponse(
                new CompletionMessageResponse(Role::from($choice['message']['role']), $choice['message']['content'],
                    $choice['message']['functions_state_id'] ?? null,
                    null),
                $choice['index'],
                FinishReason::from($choice['finish_reason'])
            );
        }
        $created = new \DateTimeImmutable();
        $created = $created->setTimestamp($data['created']);

        return new CompletionResponse(
            $choices,
            $created,
            $data['model'],
            $data['object'],
            new UsageTokens(
                $data['usage']['prompt_tokens'],
                $data['usage']['completion_tokens'],
                $data['usage']['total_tokens'],
            )
        );

    }
}