<?php
declare(strict_types=1);

namespace Talismanfr\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Talismanfr\GigaChat\API\Requests\FilePathRequest;

class FilePathRequestTest extends TestCase
{

    public function test__construct()
    {
        $path = __DIR__ . '/../../../Support/giga.jpeg';
        $request = new FilePathRequest($path);
        self::assertInstanceOf(StreamInterface::class, $request);

        self::expectException(\InvalidArgumentException::class);
        new FilePathRequest($path, 2);

        return $request;
    }

    /**
     * @return void
     * @depends test__construct
     */
    public function testGetStream(FilePathRequest $request)
    {
        self::assertInstanceOf(StreamInterface::class, $request->getStream());
    }
}
