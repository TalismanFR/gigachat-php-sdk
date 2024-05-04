<?php
declare(strict_types=1);

namespace Edvardpotter\GigaChat\Type;

class Usage implements ArrayConverterInterface
{
    private int $promptTokens;
    private int $completionTokens;
    private int $totalTokens;
    private int $systemTokens;

    public function __construct(
        int $promptTokens,
        int $completionTokens,
        int $totalTokens,
        int $systemTokens
    )
    {
        $this->promptTokens = $promptTokens;
        $this->completionTokens = $completionTokens;
        $this->totalTokens = $totalTokens;
        $this->systemTokens = $systemTokens;
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

    public function getSystemTokens(): int
    {
        return $this->systemTokens;
    }

    public static function createFromArray(array $array): self
    {
        return new Usage(
            $array['prompt_tokens'],
            $array['completion_tokens'],
            $array['total_tokens'],
            $array['system_tokens'] ?? 0,
        );
    }
}
