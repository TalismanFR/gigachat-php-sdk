<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Exception;

use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;

class ErrorGetEmbeddingsException extends \Exception
{
    #[Pure] public function __construct(private ResponseInterface $response, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

}