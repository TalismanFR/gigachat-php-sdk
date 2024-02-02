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
use Edvardpotter\GigaChat\GigaChatOAuth;
use Edvardpotter\GigaChat\Type\Message;

// https://gu-st.ru/content/Other/doc/russiantrustedca.pem
$cert = __DIR__ . '/russiantrustedca.pem';

$oauthClient = new GigaChatOAuth(
    'client_id',
    'client_secret',
    $cert // false для отключения проверки сертификата
);

$accessToken = $oauthClient->getAccessToken();
echo $accessToken->getAccessToken();
echo $accessToken->isExpired();

$client = new GigaChat(
    $accessToken->getAccessToken(),
    $cert
);

$models = $client->getModels();
foreach ($models as $model) {
    echo $model->getId();
    echo $model->getObject();
    echo $model->getOwnedBy();
}

$tokensCount = $client->tokensCount('GigaChat:latest', 'Напиши интересный факт о сбербанке');
echo $tokensCount->getObject();
echo $tokensCount->getTokens();
echo $tokensCount->getCharacters();

$completion = $client->chatCompletions([
    new Message(
        'Напиши интересный факт о сбербанке'
    )
]);

foreach ($completion->getChoices() as $choice) {
    echo $choice->getMessage()->getContent();
    echo $choice->getMessage()->getRole();
}

$embeddings = $client->getEmbeddings(['1234']);

$stream = $client->getFile('file_id');
file_put_contents('file_name.jpg', $stream);
```
