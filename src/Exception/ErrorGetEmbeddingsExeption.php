<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Exception;

use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;

class ErrorGetEmbeddingsExeption extends \Exception
{
    #[Pure] public function __construct(ResponseInterface $response, string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}