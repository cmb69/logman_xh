<?php

namespace Logman;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class PluginInfoTest extends TestCase
{
    public function testDisplaysPluginInfo(): void
    {
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
        $sut = new PluginInfo("./", new FakeSystemChecker(), $view);
        Approvals::verifyHtml($sut("en"));
    }
}
