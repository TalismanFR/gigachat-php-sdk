<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

enum Scope: string
{
    /**
     * @see https://developers.sber.ru/docs/ru/gigachat/api/tariffs#platnye-pakety-pri-rabote-po-predoplatnoy-sheme
     * Доступ для ИП и юридических лиц по предоплате
     */
    case GIGACHAT_API_B2B = 'GIGACHAT_API_B2B';

    /**
     * @see https://developers.sber.ru/docs/ru/gigachat/api/legal-postpaid
     * Доступ для ИП и юридических лиц по постоплате
     */
    case GIGACHAT_API_CORP = 'GIGACHAT_API_CORP';

    /**
     * @see https://developers.sber.ru/docs/ru/gigachat/individuals-quickstart#shag-1-sozdayte-proekt-giga-chat-api
     * Доступ для физ. лиц
     */
    case GIGACHAT_API_PERS = 'GIGACHAT_API_PERS';
}