<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Exception;

use Psr\Http\Message\ResponseInterface;

class ErrorFilesException extends \Exception
{
    public function __construct(private ResponseInterface $response, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

}