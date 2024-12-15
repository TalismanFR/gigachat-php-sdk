# GigaChat PHP SDK

PHP API SDK для [GigaChat](https://developers.sber.ru/docs/ru/gigachat/overview/).

## Установка

Установите последнюю версию

```bash
$ composer require talismanfr/gigachat-php-sdk
```

## Требования

PHP >= 8.3

## Как использовать

### Если необходимо просто получить не обработанный ответ от апи
```php
$auth = new GigaChatOAuth(
            'CLIENT_ID',
            'SECRET_ID',
            false, // отключаем валидацию https
            Scope::GIGACHAT_API_PERS
        );
// создаем экземпляр АПИ
$api = new GigaChatApi($auth);
$factory = new DialogFactory();
// формируем объект диалога с system и user промтом. Дефолтные значения настроек.
$dialog = $factory->dialogBase('Ты эксперт в футболе.', 'Сколько должно быть игроков на поле?', Model::createGigaChatPlus());
$response = $api->completions($dialog);
echo $response->getBody()->__toString();
```
>output
```json
{
  "choices": [
    {
      "message": {
        "content": "На поле должно быть 11 игроков от каждой команды.",
        "role": "assistant"
      },
      "index": 0,
      "finish_reason": "stop"
    }
  ],
  "created": 1726261876,
  "model": "GigaChat-Plus:3.1.25.3",
  "object": "chat.completion",
  "usage": {
    "prompt_tokens": 24,
    "completion_tokens": 13,
    "total_tokens": 37
  }
}
```

### Чтобы использовать на полную возможность библиотеки и вести диалог с GPT
```php
$auth = new GigaChatOAuth(
            'CLIENT_ID',
            'SECRET_ID',
            false, // отключаем валидацию https
            Scope::GIGACHAT_API_PERS
        );
// создаем экземпляр АПИ
$api = new GigaChatApi($auth);
// создаем экзепляр сервиса для работы с апи GigaChat
$service = new GigaChatService($api, new GigaChatMapper());
// формируем стартовый диалог
$messages = [
            new Message(0, 'Ты эксперт в футболе.', Role::SYSTEM, null),
            new Message(1, 'Сколько должно быть игроков на поле?', Role::USER, null),
        ];

// при создания объета диалога можно указать температуру, top_p, а так же зарегистрировать функции
$dialog = Dialog(
    Model::createGigaChatPlus(),
    new Messages(...$messages)
);
// делаем запрос к апи, в ответе получаем объект
// `Talismanfr\GigaChat\Service\Response\CompletionResponse`
$result = $service->completions($dialog);
echo $result->->choices[0]->message->content;
// output:На поле должно быть 11 игроков от каждой команды.

// в объект $dialog добавил ответ от GPT и вы можете продолжить диалог
$dialog->addMessage(new Message(0, 'Может быть что на поле меньше игроков?', Role::USER));
$service->completions($dialog);
// как и в превом запросе, ответ сразу смапится в объект $dialog
// вы можете получить список всех сообщений в рамках текущего контекста
/** @var \Talismanfr\GigaChat\Domain\VO\Message[] $messages */
$messages = $dialog->getMessages()->getMessages();
echo json_encode($dialog);
```
>output
```json
{
  "model": "GigaChat-Plus",
  "messages": [
    {
      "role": "system",
      "content": "Ты эксперт в футболе.",
      "function_state_id": null
    },
    {
      "role": "user",
      "content": "Сколько должно быть игроков на поле?",
      "function_state_id": null
    },
    {
      "role": "assistant",
      "content": "На поле должно быть 11 игроков от каждой команды.",
      "function_state_id": null
    },
    {
      "role": "user",
      "content": "Может быть что на поле меньше игроков?",
      "function_state_id": null
    },
    {
      "role": "assistant",
      "content": "Да, если команда получает предупреждение (желтую или красную карточку), то игрок должен покинуть поле. В этом случае команда может продолжить игру с десятью или девятью игроками.",
      "function_state_id": null
    }
  ],
  "temperature": 0.2,
  "max_tokens": 1024,
  "top_p": 0.1,
  "repetition_penalty": 1
}
```
### Использование функций
```php
$factory = new DialogFactory();
$auth = new GigaChatOAuth(
            'CLIENT_ID',
            'SECRET_ID',
            false, // отключаем валидацию https
            Scope::GIGACHAT_API_PERS
        );
// создаем экземпляр АПИ
$api = new GigaChatApi($auth);
// создаем экзепляр сервиса для работы с апи GigaChat
$service = new GigaChatService($api, new GigaChatMapper());
// формируем стартовый диалог
$messages = [
            new Message(0, 'Ты эксперт в футболе.', Role::SYSTEM, null),
        ];
// при создания объета диалога можно указать температуру, top_p, а так же зарегистрировать функции
$dialog = Dialog(
    Model::createGigaChatPlus(),
    new Messages(...$messages)
);
// Создаем объект функции и добавляем к диалогу
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

// получаем последнее сообщение в диалоге и если это вызов функции
// то buildFunctionResult вернет сообщение для ответа на функцию
// так же можете получить объект Talismanfr\GigaChat\Domain\VO\FunctionCall из $result->choices[0]->message->function_call
$message = $dialog->getMessages()->getLastMessage();
$messageFunctionResponse = $message->buildFunctionResult(
    json_encode(['player_number_name' => 'Всеволод Михайлович Бобров'], JSON_UNESCAPED_UNICODE)
);
// добавляем в диалог ответ на функцию и снова обращаемся к GPT
$dialog->addMessage($messageFunctionResponse);

$result = $service->completions($dialog);

// новое сообщение уже содержит ответ от GPT с использованием ответа на нашу функцию
$message = $dialog->getMessages()->getLastMessage();

echo $message->getContent();
// output: Под десятым номером в "Металлург-Кузбасс" играет Всеволод Михайлович Бобров.

// Полная структура диалога в итоге будет выглядеть так
echo json_encode($dialog);       
```
> output
```json
{
	"model": "GigaChat-Plus",
	"messages": [
		{
			"role": "user",
			"content": "Кто играет под десятым номером в металлурге-кузбасс?",
			"function_state_id": null,
			"function_call": null,
			"name": null
		},
		{
			"role": "assistant",
			"content": "",
			"function_state_id": null,
			"function_call": {
				"name": "player_number_name",
				"arguments": {
					"player_number": 10,
					"soccer_club_name": "Металлург-Кузбасс"
				}
			},
			"name": null
		},
		{
			"role": "function",
			"content": "{\"player_number_name\":\"Всеволод Михайлович Бобров\"}",
			"function_state_id": null,
			"function_call": null,
			"name": "player_number_name"
		},
		{
			"role": "assistant",
			"content": "Под десятым номером в \"Металлург-Кузбасс\" играет Всеволод Михайлович Бобров.",
			"function_state_id": null,
			"function_call": null,
			"name": null
		}
	],
	"temperature": 0.2,
	"max_tokens": 1024,
	"top_p": 0.1,
	"repetition_penalty": 1,
	"function_call": "auto",
	"functions": [
		{
			"name": "player_number_name",
			"parameters": {
				"properties": {
					"soccer_club_name": {
						"type": "string",
						"description": "Название футбольного клуба"
					},
					"player_number": {
						"type": "integer",
						"description": "Номер игрока в футбольном клубе"
					},
					"soccer_league_name": {
						"type": "string",
						"description": "Название футбольной лиги",
						"enum": [
							"Российская Премьер-лига",
							"Первая лига",
							"Вторая лига"
						]
					}
				},
				"type": "object",
				"required": [
					"soccer_club_name",
					"player_number"
				]
			},
			"description": "Возвращает фамилию имя и отчество игрока играющего в футбольном клубе под определенным номером",
			"few_shot_examples": [
				{
					"request": "Кто играет в зените под первым номером?",
					"params": {
						"soccer_club_name": "Зенит",
						"player_number": 1
					}
				}
			]
		}
	]
}
```
### Handler для автоматической обработки function_call
Возможно использовать свои обработчики для каждого ответа GPT с `function_call`.
В либе для этого используется `psr/event-dispatcher`.

Если приходит ответ от GPT с запросом на вызов функции то при наличии `EventDispatcher` в объекте `Dialog` кидается событие
`FunctionCallEvent`. На это событие можно подписаться любым удобным вам способом, тем самым обеспечив обработку функции.

Ниже и в тестах приведен пример с использованием `symfony/event-dispatcher`.
```php
// ... previous build $dialog and functions property

// передаем в диалог экземпляр EventDispatcher (это можно сделать и при создании диалог через конструктор)
$ed = new EventDispatcher();
$dialog->setEventDispatcher($ed);
// добавляем к диспечеру событий обработчик
$ed->addSubscriber(new FunctionCallSubscriber());
// обращаемся к GPT
$service->completions($dialog);
// если gigachat вернул function_call то подписчик автоматически выполнится
```
> Код подписчика
```php
class FunctionCallSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [FunctionCallEvent::class => 'functionCall'];
    }

    function functionCall(FunctionCallEvent $event)
    {
        $dialog = $event->getDialog();
        $function_name = $event->getMessageFunctionCall()->getFunctionCall()->getName();
        // если это нужный нам вызов функции, то формируем ответ и добавляем в диалог
        if ($function_name === 'player_number_name') {
            $response = $event->getMessageFunctionCall()->buildFunctionResult(json_encode(['player_number_name' => 'Иванов Иван Иванович']));
            $dialog->addMessage($response);
        }
    }
}
```
## Работа с файловым хранилищем
В разделе документации

[Здесь](docs/FILES.MD)

## Работа с историей чата
[Здесь](docs/HISTORY.MD)

## Tests
Чтобы запустить интеграционные тесты укажите свои client_id и secret_id в 
файле `phpunit.xml.dist`
```xml
<php>
    <ini name="error_reporting" value="-1"/>
    <env name="CLIENT_ID" value="00000000-0000-0000-0000-000000000000"/>
    <env name="SECRET_ID" value="00000000-0000-0000-0000-000000000000"/>
</php>
```