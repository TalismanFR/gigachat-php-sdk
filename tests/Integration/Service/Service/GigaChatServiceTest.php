<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Integration\Service\Service;

use PHPUnit\Framework\TestCase;
use Talismanfr\GigaChat\API\Auth\GigaChatOAuth;
use Talismanfr\GigaChat\API\GigaChatApi;
use Talismanfr\GigaChat\Domain\VO\FewShotExample;
use Talismanfr\GigaChat\Domain\VO\FunctionCall;
use Talismanfr\GigaChat\Domain\VO\FunctionParameters;
use Talismanfr\GigaChat\Domain\VO\FunctionProperties;
use Talismanfr\GigaChat\Domain\VO\FunctionProperty;
use Talismanfr\GigaChat\Domain\VO\Message;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Models;
use Talismanfr\GigaChat\Domain\VO\Role;
use Talismanfr\GigaChat\Domain\VO\Scope;
use Talismanfr\GigaChat\Factory\DialogFactory;
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
        self::assertEquals('model', $model->getObject());
    }

    /**
     * @depends test__construct
     */
    public function testCompletions(GigaChatService $service)
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('Ты эксперт в футболе.', 'Сколько должно быть игроков на поле?', Model::createGigaChatPlus());
        $service->completions($dialog);
        self::assertCount(3, $dialog->getMessages()->getMessages());
        $dialog->addMessage(new Message(0, 'Может быть что на поле меньше игроков?', Role::USER));
        $service->completions($dialog);
        self::assertCount(5, $dialog->getMessages()->getMessages());
    }

    /**
     * @depends test__construct
     */
    public function testCompletionsWithFunction(GigaChatService $service)
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('Ты эксперт в футболе.', 'Сколько должно быть игроков на поле?', Model::createGigaChatPlus());
        $service->completions($dialog);
        self::assertCount(3, $dialog->getMessages()->getMessages());
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
        $dialog->addMessage(new Message(0, 'Кто играет под десятым номером в металлурге-кузбасс?'));
        $result = $service->completions($dialog);
        self::assertCount(5, $dialog->getMessages()->getMessages());
        self::assertInstanceOf(FunctionCall::class, $result->choices[0]->message->function_call, json_encode($result->choices, JSON_UNESCAPED_UNICODE));
        $message = $dialog->getMessages()->getLastMessage();
        $messageFunctionResponse = $message->buildFunctionResult(json_encode(['player_number_name' => 'Всеволод Михайлович Бобров'], JSON_UNESCAPED_UNICODE));
        self::assertInstanceOf(Message::class, $messageFunctionResponse);
        $dialog->addMessage($messageFunctionResponse);
//        echo json_encode($dialog, JSON_UNESCAPED_UNICODE);

        $result = $service->completions($dialog);
        self::assertCount(7, $dialog->getMessages()->getMessages());
        $message = $dialog->getMessages()->getLastMessage();
        self::assertStringContainsStringIgnoringCase('Всеволод', $message->getContent());
        self::assertEquals($result->choices[0]->message->content, $message->getContent(), json_encode($result,JSON_UNESCAPED_UNICODE));
    }
}
