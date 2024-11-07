<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

enum Purpose: string
{
    case GENERAL = 'general';

    case TEXT2IMAGE = 'text2image';
}
