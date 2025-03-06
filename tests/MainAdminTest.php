<?php

namespace Logman;

use ApprovalTests\Approvals;
use Logman\Infra\View;
use Logman\Model\Entry;
use Logman\Model\Logfile;
use PHPUnit\Framework\TestCase;

class MainAdminTest extends TestCase
{
    public function testDisplaysLogfile(): void
    {
        $logfile = $this->createStub(Logfile::class);
        $logfile->method("find")->willReturn([
            new Entry("2023-01-30 14:00:05", "info", "XH", "login", "login from ::1"),
        ]);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
        $sut = new MainAdmin($logfile, $view);
        $_GET = [
            "logman_timestamp" => "2025",
            "logman_level" => "info",
            "logman_module" => "XH",
            "logman_category" => "login",
            "logman_description" => "from"
        ];
        Approvals::verifyHtml($sut());
    }
}
