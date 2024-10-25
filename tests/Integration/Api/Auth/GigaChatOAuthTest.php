<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Integration\Api\Auth;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Talismanfr\GigaChat\API\Auth\GigaChatOAuth;
use Talismanfr\GigaChat\API\Auth\VO\AccessToken;
use Talismanfr\GigaChat\API\Contract\GigaChatOAuthInterface;
use Talismanfr\GigaChat\Domain\VO\Scope;
use Talismanfr\GigaChat\Exception\ErrorGetAccessTokenException;

class GigaChatOAuthTest extends TestCase
{

    public function test__construct()
    {
        $auth = new GigaChatOAuth(
            getenv('CLIENT_ID'),
            getenv('SECRET_ID'),
            Scope::GIGACHAT_API_CORP,
        );
        self::assertInstanceOf(GigaChatOAuthInterface::class, $auth);
        return $auth;
    }

    /**
     * @depends test__construct
     */
    public function testGetAccessToken(GigaChatOAuth $auth)
    {
        $accessToken = $auth->getAccessToken(Uuid::uuid4());
        self::assertInstanceOf(AccessToken::class, $accessToken);
        self::assertFalse($accessToken->isExpired());

        $authError = new GigaChatOAuth('fail', 'fail', Scope::GIGACHAT_API_CORP);
        self::expectException(ErrorGetAccessTokenException::class);
        $authError->getAccessToken(Uuid::uuid4());
    }
}
