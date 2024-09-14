<?php
declare(strict_types=1);

namespace Talismanfr\GigaChat\Domain\VO;

final class FewShotExample implements \JsonSerializable
{

    /**
     * @param string $request
     * @param array $params ['name' => 'value']
     */
    public function __construct(
        private string $request,
        private array  $params
    )
    {

    }

    public function jsonSerialize(): mixed
    {
        return [
            'request' => $this->request,
            'params' => $this->params,
        ];
    }
}