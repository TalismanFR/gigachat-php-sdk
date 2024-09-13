<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Service\Response;

use Talismanfr\GigaChat\Domain\VO\Role;

final class CompletionMessageResponse implements \JsonSerializable
{
    public function __construct(
        readonly Role    $role,
        readonly string  $content,
        readonly ?string $functions_state_id,
        readonly ?FunctionCallResponse $function_call

    )
    {

    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}