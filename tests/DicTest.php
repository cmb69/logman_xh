<?php

namespace Logman;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_cf, $plugin_tx;

        $pth = ["folder" => ["plugins" => ""], "file" => ["log" => ""]];
        $plugin_cf = ["logman" => []];
        $plugin_tx = ["logman" => []];
    }

    public function testMakesPluginInfo(): void
    {
        $this->assertInstanceOf(PluginInfo::class, Dic::makePluginInfo());
    }

    public function testMakesMainAdmin(): void
    {
        $this->assertInstanceOf(MainAdmin::class, Dic::makeMainAdmin());
    }
}
