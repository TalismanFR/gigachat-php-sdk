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