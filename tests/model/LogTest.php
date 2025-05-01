<?php

namespace Logman\Model;

use PHPUnit\Framework\TestCase;

/** @small */
class LogTest extends TestCase
{
    /**
     * @see https://github.com/cmb69/logman_xh/issues/3
     * @dataProvider corruptLogLines
     */
    public function testHandlesCorruptLogLines(string $contents, Log $expected): void
    {
        $actual = Log::fromString($contents, "irrelevant");
        $this->assertEquals($expected, $actual);
    }

    public function corruptLogLines(): array
    {
        return [
            "empty" => [
                "",
                new Log([]),
            ],
            "too short" => [
                "2023-01-30 14:00:05\tinfo\tXH\tlogin",
                new Log([new Entry("2023-01-30 14:00:05", "info", "XH", "login", "")]),
            ],
            "too long" => [
                "2023-01-30 14:00:05\tinfo\tXH\tlogin\tlogin from ::1\trest",
                new Log([new Entry("2023-01-30 14:00:05", "info", "XH", "login", "login from ::1\trest")]),
            ]
        ];
    }
}
