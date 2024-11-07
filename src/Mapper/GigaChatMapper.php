<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Mapper;

use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Talismanfr\GigaChat\Domain\VO\Embedding;
use Talismanfr\GigaChat\Domain\VO\FinishReason;
use Talismanfr\GigaChat\Domain\VO\FunctionCall;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Domain\VO\Purpose;
use Talismanfr\GigaChat\Domain\VO\Role;
use Talismanfr\GigaChat\Domain\VO\TokensCount;
use Talismanfr\GigaChat\Domain\VO\UsageTokens;
use Talismanfr\GigaChat\Service\Response\CompletionChoiceResponse;
use Talismanfr\GigaChat\Service\Response\CompletionMessageResponse;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;
use Talismanfr\GigaChat\Service\Response\FileInfoResponse;
use Talismanfr\GigaChat\Service\Response\FilesResponse;
use Talismanfr\GigaChat\Service\Response\LoadFileResponse;

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
            $choices[] = new CompletionChoiceResponse(
                new CompletionMessageResponse(Role::from($choice['message']['role']), $choice['message']['content'],
                    $choice['message']['functions_state_id'] ?? null,

                    isset($choice['message']['function_call']) ? new FunctionCall($choice['message']['function_call']['name'], $choice['message']['function_call']['arguments']) : null),
                $choice['index'],
                FinishReason::from($choice['finish_reason']),
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

    /**
     * @return TokensCount[]
     */
    public function tokensCountFromResponse(ResponseInterface $response): array
    {
        $data = json_decode($response->getBody()->__toString(), true);

        $result = [];
        foreach ($data as $datum) {
            $result[] = new TokensCount($datum['tokens'], $datum['characters']);
        }

        return $result;
    }

    /**
     * @return Embedding[]
     */
    public function embeddingsFromResponse(ResponseInterface $response): array
    {
        $data = json_decode($response->getBody()->__toString(), true);

        $result = [];
        foreach ($data['data'] ?? [] as $datum) {
            $result[] = new Embedding(
                $datum['usage']['prompt_tokens'],
                $datum['embedding'],
                $datum['index']
            );
        }
        return $result;
    }

    public function loadFileFromResponse(ResponseInterface $response): LoadFileResponse
    {
        $data = json_decode($response->getBody()->__toString(), true);
        return new LoadFileResponse(
            Uuid::fromString($data['id']),
            $data['bytes'],
            $data['access_policy'],
            new \DateTimeImmutable(date('Y-m-d H:i:s', $data['created_at']), new \DateTimeZone('Europe/Moscow')),
            $data['filename'],
            Purpose::from($data['purpose']),
        );
    }

    public function fileInfoFromResponse(ResponseInterface $response): FileInfoResponse
    {
        $data = json_decode($response->getBody()->__toString(), true);
        return new FileInfoResponse(
            Uuid::fromString($data['id']),
            $data['bytes'],
            $data['access_policy'],
            new \DateTimeImmutable(date('Y-m-d H:i:s', $data['created_at']), new \DateTimeZone('Europe/Moscow')),
            $data['filename'],
            Purpose::from($data['purpose']),
        );
    }

    public function filesFromResponse(ResponseInterface $response): FilesResponse
    {
        $files = [];
        $data = json_decode($response->getBody()->__toString(), true);
        foreach ($data['data'] ?? [] as $datum) {
            $files[] = new FileInfoResponse(
                Uuid::fromString($datum['id']),
                $datum['bytes'],
                $datum['access_policy'],
                new \DateTimeImmutable(date('Y-m-d H:i:s', $datum['created_at']), new \DateTimeZone('Europe/Moscow')),
                $datum['filename'],
                Purpose::from($datum['purpose']),
            );
        }

        return new FilesResponse(...$files);
    }
}