<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Factory;

use Talismanfr\GigaChat\Domain\Entity\Dialog;
use Talismanfr\GigaChat\Domain\Entity\Messages;
use Talismanfr\GigaChat\Domain\VO\FewShotExample;
use Talismanfr\GigaChat\Domain\VO\FunctionModel;
use Talismanfr\GigaChat\Domain\VO\FunctionParameters;
use Talismanfr\GigaChat\Domain\VO\Message;
use Talismanfr\GigaChat\Domain\VO\Model;
use Talismanfr\GigaChat\Domain\VO\Role;

final class DialogFactory
{
    public function dialogBase(string $systemPrompt, string $firstMessage, ?Model $model): Dialog
    {

        if (!$model) {
            $model = Model::createGigaChatPlus();
        }
        $messages = [
            new Message(0, $systemPrompt, Role::SYSTEM, null),
            new Message(1, $firstMessage, Role::USER, null),
        ];

        return new Dialog(
            $model,
            new Messages(...$messages)
        );
    }

    /**
     * @param FewShotExample[]|null $fewShotExamples
     */
    public function functionModel(string  $name, FunctionParameters $functionParameters,
                                  ?string $description = null, ?array $fewShotExamples = null): FunctionModel
    {
        return new FunctionModel(
            $name,
            $functionParameters,
            $description,
            $fewShotExamples ?? []
        );
    }
}