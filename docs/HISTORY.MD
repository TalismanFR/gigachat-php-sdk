Работа с историей чата
==

## X-Session-ID
[Документация](https://developers.sber.ru/docs/ru/gigachat/api/keeping-context#keshirovanie-zaprosov)

При создании объекта Dialog внутри формируется свойста для X-SessionID.
Значение свойства можно получить методом 
```php
/** @var \Talismanfr\GigaChat\Domain\Entity\Dialog $dialog */
$dialog->getSessinId(); // UuidInterface
```
Это свойство подставляется в заколовок запроса при вызове метода
`completions`.

Каждое добавление нового сообщения в диалог обновляют значение `sessionId`.

Если необходимо завфиксировать и не измненять значение
```php
/** @var \Talismanfr\GigaChat\Domain\Entity\Dialog $dialog */
$dialog->holdSessionId();
```
Пример использования:
```php
$factory = new DialogFactory();
$dialog = $factory->dialogBase('Ты эксперт в футболе.',
 'Сколько должно быть игроков на поле?', Model::createGigaChatPlus());
// получаем сгенерированный sessionId
$sessionId = $dialog->getSessionId();
$service->completions($dialog);

//создаем новый диалог и присваеваем ему уже ранее используемый sessionId.
$dialog = $factory->dialogBase('Ты эксперт в футболе.',
 'Сколько должно быть игроков на поле?', Model::createGigaChatPlus());
$dialog->setSessionId($sessionId);
$service->completions($dialog);
// в заголовке запроса уйдет sessionid который использовался ранее
// и в теории это должно уменьшить кол-во токенов затраченых на запроса
```