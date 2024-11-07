<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\API\Requests;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

final class FilePathRequest implements StreamInterface
{
    private StreamInterface $stream;

    public function __construct(
        private string $pathToFile,
        private int    $maxSize = 15000000 //15 mb
    )
    {
        if (!file_exists($this->pathToFile)) {
            throw new \InvalidArgumentException("File path '{$this->pathToFile}' does not exist");
        }
        if (!is_readable($this->pathToFile)) {
            throw new \InvalidArgumentException("File path '{$this->pathToFile}' is not readable");
        }

        if (filesize($this->pathToFile) > $this->maxSize) {
            throw new \InvalidArgumentException("File path '{$this->pathToFile}' is too big. Max size: {$this->maxSize} bytes");
        }

        //todo validate mime file

        $this->stream = Utils::streamFor(fopen($this->pathToFile, 'r'));
    }

    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    public function __toString(): string
    {
        return $this->stream->__tostring();
    }

    public function close(): void
    {
        $this->stream->close();
    }

    public function detach(): void
    {
        $this->stream->detach();
    }

    public function getSize(): ?int
    {
        return $this->stream->getSize();
    }

    public function tell(): int
    {
        return $this->stream->tell();
    }

    public function eof(): bool
    {
        return $this->stream->eof();
    }

    public function isSeekable(): bool
    {
        return $this->stream->isSeekable();
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $this->stream->seek($offset, $whence);
    }

    public function rewind(): void
    {
        $this->stream->rewind();
    }

    public function isWritable(): bool
    {
        return $this->stream->isWritable();
    }

    public function write(string $string): int
    {
        return $this->stream->write($string);
    }

    public function isReadable(): bool
    {
        return $this->stream->isReadable();
    }

    public function read(int $length): string
    {
        return $this->stream->read($length);
    }

    public function getContents(): string
    {
        return $this->stream->getContents();
    }

    public function getMetadata(?string $key = null): void
    {
        $this->stream->getMetadata($key);
    }
}