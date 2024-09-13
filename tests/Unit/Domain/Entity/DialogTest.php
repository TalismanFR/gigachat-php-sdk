<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Unit\Domain\Entity;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Role;
use Talismanfr\GigaChat\Domain\VO\UsageTokens;
use Talismanfr\GigaChat\Factory\DialogFactory;
use Talismanfr\GigaChat\Mapper\GigaChatMapper;

class DialogTest extends TestCase
{

    private const BASE_JSON = '{"model":"GigaChat-Pro","messages":[{"role":"system","content":"You AI","function_state_id":null},{"role":"user","content":"Hi, what are you?","function_state_id":null}],"temperature":0.2,"max_tokens":1024,"top_p":0.1,"repetition_penalty":1}';

    public function testJsonSerialize()
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('You AI', 'Hi, what are you?', Model::createGigaChatPro());

        $json = json_encode($dialog->jsonSerialize(), JSON_UNESCAPED_UNICODE);

        self::assertEquals($json, self::BASE_JSON);
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
