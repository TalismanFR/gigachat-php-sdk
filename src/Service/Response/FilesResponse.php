<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Response;

final class FilesResponse implements \JsonSerializable
{
    /**
     * @var FileInfoResponse[]
     */
    public readonly array $files;

    public function __construct(FileInfoResponse ...$files)
    {
        $this->files = $files;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}