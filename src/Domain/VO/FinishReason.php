<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

enum FinishReason: string
{
    case STOP = 'stop';
    case LENGTH = 'length';

    case FUNCTION_CALL = 'function_call';

    case BLACKLIST = 'blacklist';
}