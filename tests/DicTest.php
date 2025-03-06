<?php

namespace Logman;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_tx;

        $pth = ["folder" => ["plugins" => ""], "file" => ["log" => ""]];
        $plugin_tx = ["logman" => []];
    }

    public function testMakesMainAdmin(): void
    {
        $this->assertInstanceOf(MainAdmin::class, Dic::makeMainAdmin());
    }
}
