<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Mapper;

use Psr\Http\Message\ResponseInterface;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Models;

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
}