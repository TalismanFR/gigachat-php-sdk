# GigaChat PHP SDK

[![Latest Stable Version](http://poser.pugx.org/edvardpotter/gigachat-php-sdk/v)](https://packagist.org/packages/edvardpotter/gigachat-php-sdk) [![Total Downloads](http://poser.pugx.org/edvardpotter/gigachat-php-sdk/downloads)](https://packagist.org/packages/edvardpotter/gigachat-php-sdk) [![Latest Unstable Version](http://poser.pugx.org/edvardpotter/gigachat-php-sdk/v/unstable)](https://packagist.org/packages/edvardpotter/gigachat-php-sdk) [![License](http://poser.pugx.org/edvardpotter/gigachat-php-sdk/license)](https://packagist.org/packages/edvardpotter/gigachat-php-sdk) [![PHP Version Require](http://poser.pugx.org/edvardpotter/gigachat-php-sdk/require/php)](https://packagist.org/packages/edvardpotter/gigachat-php-sdk)

PHP API SDK для [GigaChat](https://developers.sber.ru/docs/ru/gigachat/overview/).

## Установка

Установите последнюю версию

```bash
$ composer require edvardpotter/gigachat-php-sdk
```

## Требования

PHP >= 7.4

## Как использовать

```php

<?php

require 'vendor/autoload.php';

use Edvardpotter\GigaChat\GigaChat;
use Edvardpotter\GigaChat\GigaChatDialog;
use Edvardpotter\GigaChat\GigaChatOAuth;
use Edvardpotter\GigaChat\Type\Message;
use Edvardpotter\GigaChat\Type\Model;

// https://gu-st.ru/content/Other/doc/russiantrustedca.pem
$cert = __DIR__ . '/russiantrustedca.pem';

$oauthClient = new GigaChatOAuth(
    'client_id',
    'client_secret',
    $cert // false для отключения проверки сертификата
);

// Получить токена
$accessToken = $oauthClient->getAccessToken();
echo $accessToken->getAccessToken();
echo $accessToken->isExpired();

$gigaChat = new GigaChat(
    $accessToken->getAccessToken(),
    $cert
);

// Пример отправки сообщения
$messages = [
    new Message(
        'Когда уже ИИ захватит этот мир?'
    ),
];
$completion = $gigaChat->chatCompletions($messages);

foreach ($completion->getChoices() as $choice) {
    echo $choice->getMessage()->getContent();
    echo $choice->getMessage()->getRole();
}

// Пример для работы с GigaChat в режиме диалога
$dialog = new GigaChatDialog($gigaChat);
$questionMessage = new Message('Когда уже ИИ захватит этот мир?');
$answerMessage = $dialog->getAnswer($questionMessage);

$questionMessage = new Message('Как ИИ изменятся в будущем?');
$answerMessage = $dialog->getAnswer($questionMessage);

// Сброс истории диалога
$dialog->reset();


// Получить список доступных моделей
$models = $gigaChat->getModels();
foreach ($models as $model) {
    echo $model->getId();
    echo $model->getObject();
    echo $model->getOwnedBy();
}

// Посчитать кол-во токенов для строки
$tokensCount = $gigaChat->tokensCount(Model::ID_GIGACHAT_LATEST, 'Когда уже ИИ захватит этот мир?');
echo $tokensCount->getObject();
echo $tokensCount->getTokens();
echo $tokensCount->getCharacters();

// Скачивание файла
$stream = $gigaChat->getFile('file_id');
file_put_contents('file_name.jpg', $stream);

// Создать векторные представления
$embeddings = $gigaChat->getEmbeddings(['1234']);

```
