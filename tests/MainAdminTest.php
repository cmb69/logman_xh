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
        $logfile->method("findAll")->willReturn([
            new Entry("2023-01-30 14:00:05", "info", "XH", "login", "login from ::1"),
            new Entry("2023-02-06 14:41:15", "warning", "moved", "not found", "Template3 from unknown"),
        ]);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
        $sut = new MainAdmin($logfile, $view);
        Approvals::verifyHtml($sut());
    }
}
