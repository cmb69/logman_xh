<?php

namespace Logman;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\FakeSystemChecker;
use Plib\View;

class PluginInfoTest extends TestCase
{
    public function testDisplaysPluginInfo(): void
    {
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
        $sut = new PluginInfo("./", new FakeSystemChecker(), $view);
        $request = new FakeRequest(["language" => "en"]);
        Approvals::verifyHtml($sut($request)->output());
    }
}
