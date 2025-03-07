<?php

namespace Logman;

use ApprovalTests\Approvals;
use Logman\Infra\View;
use PHPUnit\Framework\TestCase;

class PluginInfoTest extends TestCase
{
    public function testDisplaysPluginInfo(): void
    {
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
        $sut = $this->getMockBuilder(PluginInfo::class)
            ->setConstructorArgs(["./", $view])
            ->onlyMethods(["isXhVersionAtLeast", "isPhpVersionAtLeast", "isWritable"])
            ->getMock();
            $sut->expects($this->any())->method("isXhVersionAtLeast")->willReturn(true);
            $sut->expects($this->any())->method("isPhpVersionAtLeast")->willReturn(true);
            $sut->expects($this->any())->method("isWritable")->willReturn(true);
            Approvals::verifyHtml($sut("en"));
    }
}
