<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service;

use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\UuidInterface;
use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\API\Requests\EmbeddingsRequest;
use Talismanfr\GigaChat\API\Requests\LoadFileRequest;
use Talismanfr\GigaChat\API\Requests\TokensCountRequest;
use Talismanfr\GigaChat\Domain\Entity\Dialog;
use Talismanfr\GigaChat\Domain\VO\Embedding;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Domain\VO\TokensCount;
use Talismanfr\GigaChat\Exception\ErrorDownloadFilesExeption;
use Talismanfr\GigaChat\Exception\ErrorFileInfoExeption;
use Talismanfr\GigaChat\Exception\ErrorFilesExeption;
use Talismanfr\GigaChat\Exception\ErrorGetEmbeddingsExeption;
use Talismanfr\GigaChat\Exception\ErrorGetModelsExeption;
use Talismanfr\GigaChat\Exception\ErrorGetTokensCountExeption;
use Talismanfr\GigaChat\Exception\ErrorLoadFileExeption;
use Talismanfr\GigaChat\Mapper\GigaChatMapper;
use Talismanfr\GigaChat\Service\Contract\GigaChatServiceInterface;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;
use Talismanfr\GigaChat\Service\Response\FileInfoResponse;
use Talismanfr\GigaChat\Service\Response\FilesResponse;
use Talismanfr\GigaChat\Service\Response\LoadFileResponse;

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

    /**
     * @return TokensCount[]
     * @throws ErrorGetTokensCountExeption
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function tokensCount(TokensCountRequest $request): array
    {
        $response = $this->api->tokensCount($request);
        if ($response->getStatusCode() !== 200) {
            throw  new ErrorGetTokensCountExeption($response, 'Error get tokens count', $response->getStatusCode());
        }

        return $this->mapper->tokensCountFromResponse($response);
    }

    /**
     * @return Embedding[]
     * @throws ErrorGetEmbeddingsExeption
     */
    public function embeddings(EmbeddingsRequest $request): array
    {
        $response = $this->api->embeddings($request);
        if ($response->getStatusCode() !== 200) {
            throw  new ErrorGetEmbeddingsExeption($response, 'Error get embeddings', $response->getStatusCode());
        }

        return $this->mapper->embeddingsFromResponse($response);
    }

    public function loadFile(LoadFileRequest $request): LoadFileResponse
    {
        $response = $this->api->loadFile($request);
        if ($response->getStatusCode() !== 200) {
            throw  new ErrorLoadFileExeption($response, 'Error load file', $response->getStatusCode());
        }

        return $this->mapper->loadFileFromResponse($response);
    }

    public function fileInfo(UuidInterface $uuid): FileInfoResponse
    {
        $response = $this->api->fileInfo($uuid->toString());
        if ($response->getStatusCode() !== 200) {
            throw  new ErrorFileInfoExeption($response, 'Error get file info', $response->getStatusCode());
        }

        return $this->mapper->fileInfoFromResponse($response);
    }

    public function files(): FilesResponse
    {
        $response = $this->api->files();
        if ($response->getStatusCode() !== 200) {
            throw  new ErrorFilesExeption($response, 'Error get files info', $response->getStatusCode());
        }

        return $this->mapper->filesFromResponse($response);
    }

    public function downloadFile(UuidInterface $uuid): StreamInterface
    {
        $response = $this->api->downloadFile($uuid->toString());
        if ($response->getStatusCode() !== 200) {
            throw  new ErrorDownloadFilesExeption($response, 'Error download file', $response->getStatusCode());
        }

        return $response->getBody();
    }
}