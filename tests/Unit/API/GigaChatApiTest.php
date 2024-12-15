<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Unit\API;

use http\Message;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Talismanfr\GigaChat\API\Auth\VO\AccessToken;
use Talismanfr\GigaChat\API\Contract\GigaChatOAuthInterface;
use Talismanfr\GigaChat\API\GigaChatApi;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Factory\DialogFactory;
use Talismanfr\Tests\Support\TestClient;

class GigaChatApiTest extends TestCase
{

    public function test__construct()
    {
        $auth = $this->createStub(GigaChatOAuthInterface::class);
        $auth->method('getAccessToken')->willReturn(new AccessToken('3232', new \DateTimeImmutable('+2 hours')));
        $api = new GigaChatApi($auth, new TestClient());
        $this->assertInstanceOf(GigaChatApi::class, $api);
        return $api;
    }

    /**
     * @depends test__construct
     */
    public function testSessionId(GigaChatApi $api)
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('Ты эксперт в футболе.', 'Сколько должно быть игроков на поле?', Model::createGigaChatPlus());
        $sessionId = $dialog->getSessionId();
        self::assertInstanceOf(UuidInterface::class, $sessionId);
        $response = $api->completions($dialog);
        $headers = $response->getHeaders();
        self::assertArrayHasKey('X-Session-ID', $headers);
        self::assertEquals($sessionId->toString(), $headers['X-Session-ID'][0]);
        $dialog->holdSesseionId();
        $dialog->addMessage(new \Talismanfr\GigaChat\Domain\VO\Message(0,'test'));
        $response = $api->completions($dialog);
        $headers = $response->getHeaders();
        self::assertArrayHasKey('X-Session-ID', $headers);
        self::assertEquals($sessionId->toString(), $headers['X-Session-ID'][0]);
    }
}
