Работа с файлами
==
## Загрузка файла в хранилище
```php
// подготовка класса сервиса
$factory = new DialogFactory();
$auth = new GigaChatOAuth(
            'CLIENT_ID',
            'SECRET_ID',
            false, // отключаем валидацию https
            Scope::GIGACHAT_API_PERS
        );
// создаем экземпляр АПИ
$api = new GigaChatApi($auth);
// создаем экземпляр сервиса для работы с апи GigaChat
$service = new GigaChatService($api, new GigaChatMapper());

```
Для отправки файла необходима реализация `Psr\Http\Message\StreamInterface`.
Если файл загружается с файловой системы, то рекомендуем использовать класс `FilePathRequest`.
Оне реализует необходимый интерфейс, а также еще до отправки проводит валидации файла на размер и тип.

```php
use Talismanfr\GigaChat\API\Requests\FilePathRequest;
use Talismanfr\GigaChat\API\Requests\LoadFileRequest;

$path = '/path/to/file.jpeg';
$file = new FilePathRequest($path);
$request = new LoadFileRequest($file, basename($path));

/** @var \Talismanfr\GigaChat\Service\Response\LoadFileResponse $load */
$load = $service->loadFile($request);
// в случае ошибки бросается искючение ErrorLoadFileExeption

echo $load->id->toString();
// b6360f95-3272-409c-ad1a-32ec737f2952
```
## Получение информации о загруженном файле
```php
/** @var \Talismanfr\GigaChat\Service\Response\FileInfoResponse $info **/
$info = $service->fileInfo(Uuid::fromString('b6360f95-3272-409c-ad1a-32ec737f2952'));
echo $info->id->toString();
//b6360f95-3272-409c-ad1a-32ec737f2952
echo $info->bytes;
//5005
```
## Получение информации о загруженных файлах
```php
/** @var \Talismanfr\GigaChat\Service\Response\FilesResponse $files **/
$files = $service->files();

/** @var \Talismanfr\GigaChat\Service\Response\FileInfoResponse $file */
foreach ($files->files as $file){
    echo $file->id->toString().PHP_EOL;
}
```
## Загрузка файла из хранилища
```php
/** @var \Psr\Http\Message\StreamInterface **/
$stream = $service->downloadFile(Uuid::fromString('81fe1cb5-4263-4a6c-9896-8662f1d59e86'));
file_put_contents('/path/to/save/file.jpg',$service->__toString());
```