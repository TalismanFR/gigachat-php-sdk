<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Unit\Service;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Talismanfr\GigaChat\API\Contract\GigaChatApiInterface;
use Talismanfr\GigaChat\API\Contract\GigaChatOAuthInterface;
use Talismanfr\GigaChat\Domain\VO\FinishReason;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Role;
use Talismanfr\GigaChat\Factory\DialogFactory;
use Talismanfr\GigaChat\Mapper\GigaChatMapper;
use Talismanfr\GigaChat\Service\Contract\GigaChatServiceInterface;
use Talismanfr\GigaChat\Service\GigaChatService;
use Talismanfr\GigaChat\Service\Response\CompletionChoiceResponse;
use Talismanfr\GigaChat\Service\Response\CompletionMessageResponse;
use Talismanfr\GigaChat\Service\Response\CompletionResponse;
use Talismanfr\GigaChat\Type\AccessToken;

class GigaChatServiceTest extends TestCase
{

    public function test__construct()
    {
        $auth = $this->createStub(GigaChatOAuthInterface::class);
        $auth->method('getAccessToken')->willReturn(new AccessToken('d', new \DateTimeImmutable('+1 day')));
        $api = $this->createStub(GigaChatApiInterface::class);
        $api->method('completions')->willReturnOnConsecutiveCalls(
            new Response(200, [], '{"choices":[{"message":{"content":"На поле должно быть 11 игроков от каждой команды.","role":"assistant"},"index":0,"finish_reason":"stop"}],"created":1726261876,"model":"GigaChat-Plus:3.1.25.3","object":"chat.completion","usage":{"prompt_tokens":24,"completion_tokens":13,"total_tokens":37}}')
        );

        $service = new GigaChatService($api, new GigaChatMapper());

        self::assertInstanceOf(GigaChatServiceInterface::class, $service);

        return $service;
    }

    /**
     * @depends test__construct
     */
    public function testCompletions(GigaChatService $service)
    {
        $factory = new DialogFactory();
        $dialog = $factory->dialogBase('test', 'test', Model::createGigaChatPlus());
        $result = $service->completions($dialog);

        self::assertInstanceOf(CompletionResponse::class, $result);
        $choice = $result->choices[0];
        self::assertInstanceOf(CompletionChoiceResponse::class, $choice);
        $message = $choice->message;
        self::assertInstanceOf(CompletionMessageResponse::class, $message);
        self::assertEquals('На поле должно быть 11 игроков от каждой команды.', $message->content);
        self::assertEquals(Role::ASSISTANT, $message->role);

        self::assertEquals(0, $choice->index);
        self::assertEquals(FinishReason::STOP, $choice->finish_reason);
        self::assertEquals('GigaChat-Plus:3.1.25.3', $result->model);
        self::assertEquals(24, $result->usage->getPromptTokens());
        self::assertEquals(13, $result->usage->getCompletionTokens());
        self::assertEquals(37, $result->usage->getTotalTokens());
    }
}
