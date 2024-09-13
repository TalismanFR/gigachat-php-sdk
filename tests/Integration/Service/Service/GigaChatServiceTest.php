<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Integration\Service\Service;

use PHPUnit\Framework\TestCase;
use Talismanfr\GigaChat\API\Auth\GigaChatOAuth;
use Talismanfr\GigaChat\API\GigaChatApi;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Domain\VO\Scope;
use Talismanfr\GigaChat\Mapper\GigaChatMapper;
use Talismanfr\GigaChat\Service\GigaChatService;

class GigaChatServiceTest extends TestCase
{

    public function test__construct()
    {
        $auth = new GigaChatOAuth(
            getenv('CLIENT_ID'),
            getenv('SECRET_ID'),
            false,
            Scope::GIGACHAT_API_CORP
        );
        $api = new GigaChatApi($auth);
        $service = new GigaChatService($api, new GigaChatMapper());
        self::assertInstanceOf(GigaChatService::class, $service);
        return $service;
    }

    /**
     * @depends test__construct
     */
    public function testModels(GigaChatService $service)
    {
        $models = $service->models();
        self::assertInstanceOf(Models::class, $models);
        self::assertArrayHasKey(0, $models->getModels());
        $model = $models->getModels()[0];
        self::assertInstanceOf(Model::class, $model);
        self::assertStringStartsWith('GigaChat', $model->getId());
        self::assertNotEmpty($model->getOwnedBy());
        self::assertEquals($model->getObject(), 'model');
    }
}
