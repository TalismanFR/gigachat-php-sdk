<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Requests;

use Psr\Http\Message\StreamInterface;
use Talismanfr\GigaChat\Domain\VO\Purpose;

final class LoadFileRequest
{
    public function __construct(
        private StreamInterface $file,
        private string          $filename = 'undefined',
        private Purpose         $purpose = Purpose::GENERAL,
    )
    {
    }

    public function getFile(): StreamInterface
    {
        return $this->file;
    }

    public function getPurpose(): Purpose
    {
        return $this->purpose;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

}