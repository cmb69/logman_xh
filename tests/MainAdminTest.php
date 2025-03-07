<?php

namespace Logman;

use ApprovalTests\Approvals;
use Logman\Infra\View;
use Logman\Model\Entry;
use Logman\Model\Logfile;
use PHPUnit\Framework\MockObject;
use PHPUnit\Framework\TestCase;

class MainAdminTest extends TestCase
{
    public function testDisplaysLogfile(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("find")->willReturn([$this->loginSuccessEntry()]);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $_GET = [
            "action" => "plugin_text",
            "logman_timestamp" => "2025",
            "logman_level" => "info",
            "logman_module" => "XH",
            "logman_category" => "login",
            "logman_description" => "from"
        ];
        Approvals::verifyHtml($sut());
    }

    /** <https://github.com/cmb69/logman_xh/issues/1> */
    public function testZeroMaxEntriesDisplaysEntries(): void
    {
        $conf = $this->conf();
        $conf["entries_max"] = "0";
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("find")->with($this->anything(), PHP_INT_MAX)
            ->willReturn([$this->loginSuccessEntry()]);
        $sut = $this->sut($conf, $logfile, $this->view());
        $_GET = [
            "action" => "plugin_text",
        ];
        $this->assertStringContainsString("2023-01-30 14:00:05", $sut());
    }

    public function testDisplaysDeleteConfirmation(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $_GET = [
            "action" => "delete",
            "logman_count" => "1",
            "logman_timestamp" => "2025",
            "logman_level" => "info",
            "logman_module" => "XH",
            "logman_category" => "login",
            "logman_description" => "from"
        ];
        Approvals::verifyHtml($sut());
    }

    public function testDeletesEntries(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("delete")->willReturn(1);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $sut->expects($this->once())->method("redirect");
        $_SERVER["QUERY_STRING"] = "";
        $_GET = [
            "action" => "delete",
            "logman_count" => "1",
            "logman_timestamp" => "2025",
            "logman_level" => "info",
            "logman_module" => "XH",
            "logman_category" => "login",
            "logman_description" => "from"
        ];
        $_POST = [
            "logman_do" => "",
        ];
        $sut();
    }

    public function testDisplaysNumberOfDeletedEntries(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("find")->willReturn([$this->loginSuccessEntry()]);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $_SERVER["QUERY_STRING"] = "";
        $_GET = [
            "action" => "",
            "logman_count" => "1",
            "logman_deleted" => "17",
            "logman_timestamp" => "2025",
            "logman_level" => "info",
            "logman_module" => "XH",
            "logman_category" => "login",
            "logman_description" => "from"
        ];
        $this->assertStringContainsString("17 entries deleted!", $sut());
    }

    private function sut(array $conf, Logfile $logfile, View $view)
    {
        return $this->getMockBuilder(MainAdmin::class)
            ->setConstructorArgs([$conf, $logfile, $view])
            ->onlyMethods(["redirect"])
            ->getMock();
    }

    private function conf(): array
    {
        return XH_includeVar("./config/config.php", "plugin_cf")["logman"];
    }

    private function view(): View
    {
        return new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
    }

    private function loginSuccessEntry(): Entry
    {
        return new Entry("2023-01-30 14:00:05", "info", "XH", "login", "login from ::1");
    }
}
