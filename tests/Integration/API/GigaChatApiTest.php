<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Integration\API;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Talismanfr\GigaChat\API\Auth\GigaChatOAuth;
use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\API\GigaChatApi;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Scope;
use Talismanfr\GigaChat\Factory\DialogFactory;

class GigaChatApiTest extends TestCase
{

    /**
     * @depends test__construct
     */
    public function testModels(GigaChatApi $api)
    {
        $models = $api->models();
        self::assertInstanceOf(ResponseInterface::class, $models);
        self::assertEquals(200, $models->getStatusCode());
    }

    /**
     * @depends test__construct
     */
    public function testCompletions(GigaChatApi $api)
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('Ты эксперт в футболе.', 'Сколько должно быть игроков на поле?', Model::createGigaChatPlus());
        $response = $api->completions($dialog);
        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($response->getBody()->__toString());
    }

    public function test__construct()
    {
        $auth = new GigaChatOAuth(
            getenv('CLIENT_ID'),
            getenv('SECRET_ID'),
            false,
            Scope::GIGACHAT_API_CORP
        );
        $api = new GigaChatApi($auth);
        self::assertInstanceOf(GigaChatApiInterface::class, $api);

        return $api;
    }
}
