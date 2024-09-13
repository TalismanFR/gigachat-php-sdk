<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

enum Role: string
{
    case SYSTEM = 'system';
    case USER = 'user';
    case ASSISTANT = 'assistant';
    case FUNCTION = 'function';
    case FUNCTION_IN_PROGRESS = 'function_in_progress';
}
