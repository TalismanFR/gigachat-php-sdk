<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Unit\Domain\Entity;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Talismanfr\GigaChat\Domain\VO\FewShotExample;
use Talismanfr\GigaChat\Domain\VO\FunctionParameters;
use Talismanfr\GigaChat\Domain\VO\FunctionProperties;
use Talismanfr\GigaChat\Domain\VO\FunctionProperty;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Role;
use Talismanfr\GigaChat\Domain\VO\UsageTokens;
use Talismanfr\GigaChat\Factory\DialogFactory;
use Talismanfr\GigaChat\Mapper\GigaChatMapper;

class DialogTest extends TestCase
{

    private const BASE_JSON = '{"model":"GigaChat-Pro","messages":[{"role":"system","content":"You AI","function_state_id":null},{"role":"user","content":"Hi, what are you?","function_state_id":null}],"temperature":0.2,"max_tokens":1024,"top_p":0.1,"repetition_penalty":1,"function_call":null,"functions":null}';

    private const BASE_JSON_FUNCTION = '{"name":"player_number_name","parameters":{"properties":{"soccer_club_name":{"type":"string","description":"Название футбольного клуба"},"player_number":{"type":"integer","description":"Номер игрока в футбольном клубе"},"soccer_league_name":{"type":"string","description":"Название футбольной лиги","enum":["Российская Премьер-лига","Первая лига","Вторая лига"]}},"type":"object","required":["soccer_club_name","player_number"]},"description":"Возвращает фамилию имя и отчество игрока играющего в футбольном клубе под определенным номером","few_shot_examples":[{"request":"Кто играет в зените под первым номером?","params":{"soccer_club_name":"Зенит","player_number":1}}]}';

    public function testJsonSerialize()
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('You AI', 'Hi, what are you?', Model::createGigaChatPro());

        $json = json_encode($dialog->jsonSerialize(), JSON_UNESCAPED_UNICODE);
        self::assertEquals($json, self::BASE_JSON);

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

        $json = json_encode($function, JSON_UNESCAPED_UNICODE);
        self::assertEquals($json, self::BASE_JSON_FUNCTION);
        $dialog->addFunction($function);

        self::assertCount(1, $dialog->getFunctions()->getFunctions());
        self::assertEquals('auto', $dialog->getFunctionCall()->jsonSerialize());
    }

    public function testProcessedCompletionResponse()
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('You AI', 'Hi, what are you?', Model::createGigaChatPro());

        $mapper = new GigaChatMapper();
        $result = $mapper->completionFromResponse(new Response(200, [], '{"choices":[{"message":{"content":"На поле должно быть 11 игроков от каждой команды.","role":"assistant"},"index":0,"finish_reason":"stop"}],"created":1726261876,"model":"GigaChat-Plus:3.1.25.3","object":"chat.completion","usage":{"prompt_tokens":24,"completion_tokens":13,"total_tokens":37}}'));
        $dialog->processedCompletionResponse($result);

        self::assertCount(3, $dialog->getMessages()->getMessages());
        self::assertInstanceOf(UsageTokens::class, $dialog->getUsage());
        $messages = $dialog->getMessages();
        self::assertEquals(2, $messages->getMaxIndex());
        $message = $messages->getMessages()[2];

        self::assertEquals(Role::ASSISTANT, $message->getRole());
        self::assertEquals('На поле должно быть 11 игроков от каждой команды.', $message->getContent());
        self::assertEquals(2, $message->getIndex());

        $json = json_encode($dialog, JSON_UNESCAPED_UNICODE);
        echo $json;
    }

}
