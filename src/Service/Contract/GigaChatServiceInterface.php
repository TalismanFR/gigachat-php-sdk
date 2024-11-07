<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Contract;

use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\UuidInterface;
use Talismanfr\GigaChat\API\Requests\EmbeddingsRequest;
use Talismanfr\GigaChat\API\Requests\LoadFileRequest;
use Talismanfr\GigaChat\API\Requests\TokensCountRequest;
use Talismanfr\GigaChat\Domain\Entity\Dialog;
use Talismanfr\GigaChat\Domain\VO\Embedding;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;
use Talismanfr\GigaChat\Service\Response\FileInfoResponse;
use Talismanfr\GigaChat\Service\Response\FilesResponse;
use Talismanfr\GigaChat\Service\Response\LoadFileResponse;

interface GigaChatServiceInterface
{
    public function models(): Models;

    public function completions(Dialog $dialog): CompletionResponse;

    public function tokensCount(TokensCountRequest $request): array;

    /**
     * @return Embedding[]
     */
    public function embeddings(EmbeddingsRequest $request): array;

    public function loadFile(LoadFileRequest $request): LoadFileResponse;

    public function fileInfo(UuidInterface $uuid): FileInfoResponse;

    public function files(): FilesResponse;

    public function downloadFile(UuidInterface $uuid): StreamInterface;

}