<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class UsageTokens implements \JsonSerializable
{

    public function __construct(
        private int $promptTokens,
        private int $completionTokens,
        private int $totalTokens
    )
    {

    }

    public function getPromptTokens(): int
    {
        return $this->promptTokens;
    }

    public function getCompletionTokens(): int
    {
        return $this->completionTokens;
    }

    public function getTotalTokens(): int
    {
        return $this->totalTokens;
    }


    public function jsonSerialize(): array
    {
        return [
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'total_tokens' => $this->totalTokens
        ];
    }
}