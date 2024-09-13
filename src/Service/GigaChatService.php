<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service;

use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Exception\ErrorGetModelsExeption;
use Talismanfr\GigaChat\Mapper\GigaChatMapper;
use Talismanfr\GigaChat\Service\Contract\GigaChatServiceInterface;

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
            echo $response->getBody()->__toString();
            throw  new ErrorGetModelsExeption($response, 'Error get models', $response->getStatusCode());
        }
        return $this->mapper->modelsFromResponse($response);
    }
}