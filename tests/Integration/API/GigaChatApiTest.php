<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Integration\API;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Talismanfr\GigaChat\API\Auth\GigaChatOAuth;
use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\API\GigaChatApi;
use Talismanfr\GigaChat\API\Requests\TokensCountRequest;
use Talismanfr\GigaChat\Domain\VO\FewShotExample;
use Talismanfr\GigaChat\Domain\VO\FunctionParameters;
use Talismanfr\GigaChat\Domain\VO\FunctionProperties;
use Talismanfr\GigaChat\Domain\VO\FunctionProperty;
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

    /**
     * @depends test__construct
     */
    public function testCompletetionsWithFunction(GigaChatApi $api)
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('Ты эксперт в футболе.', 'Как звать игрока под третьим номером в спартаке?', Model::createGigaChatPlus());
        $function = $factory->functionModel('player_number_name',
            new FunctionParameters(
                new FunctionProperties(
                    new FunctionProperty('soccer_club_name', 'string', 'Название футбольного клуба', true),
                    new FunctionProperty('player_number', 'integer', 'Номер игрока в футбольном клубе', true),
                    new FunctionProperty('soccer_league_name', 'string', 'Название футбольной лиги', false, [
                        'Российская Премьер-лига',
                        'Первая лига',
                        'Вторая лига',
                    ]),
                )
            ),
            'Возвращает фамилию имя и отчество игрока играющего в футбольном клубе под определенным номером',
            [
                new FewShotExample('Кто играет в зените под первым номером?', ['soccer_club_name' => 'Зенит', 'player_number' => 1])
            ]
        );
        $dialog->addFunction($function);
        $response = $api->completions($dialog);
        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($response->getBody()->__toString());
        self::assertStringContainsStringIgnoringCase('function_call', $response->getBody()->__toString());
    }

    /**
     * @param GigaChatApi $api
     * @return void
     * @depends test__construct
     */
    public function testTokenCount(GigaChatApi $api)
    {
        $request = new TokensCountRequest(Model::createGigaChat(), ['Тест токенов','новый']);
        $response = $api->tokensCount($request);
        self::assertEquals(200, $response->getStatusCode());
        self::assertJson($response->getBody()->__toString());
    }

    public function test__construct()
    {
        $auth = new GigaChatOAuth(
            getenv('CLIENT_ID'),
            getenv('SECRET_ID'),
            Scope::GIGACHAT_API_CORP
        );
        $api = new GigaChatApi($auth);
        self::assertInstanceOf(GigaChatApiInterface::class, $api);

        return $api;
    }
}
