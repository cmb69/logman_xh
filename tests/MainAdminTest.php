<?php

namespace Logman;

use ApprovalTests\Approvals;
use Logman\Model\Entry;
use Logman\Model\Logfile;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class MainAdminTest extends TestCase
{
    public function testDisplaysLogfile(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("find")->willReturn([
            $this->loginSuccessEntry(),
            $this->movedEntry(),
            $this->loginFailureEntry(),
        ]);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=plugin_text&logman_timestamp=2025&logman_level=info&logman_module=XH"
                . "&logman_category=login&logman_description=from",
        ]);
        Approvals::verifyHtml($sut($request));
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
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=plugin_text",
        ]);
        $this->assertStringContainsString("2023-01-30 14:00:05", $sut($request));
    }

    public function testDisplaysDeleteConfirmation(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=delete&logman_count=1",
        ]);
        Approvals::verifyHtml($sut($request));
    }

    public function testDeletesEntries(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("delete")->willReturn(1);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $sut->expects($this->once())->method("redirect");
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=delete&logman_count=1",
            "post" => [
                "logman_do" => "",
            ],
        ]);
        $sut($request);
    }

    public function testDisplaysNumberOfDeletedEntries(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("find")->willReturn([$this->loginSuccessEntry()]);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=&logman_deleted=17",
        ]);
        $this->assertStringContainsString("17 entries deleted!", $sut($request));
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

    private function movedEntry(): Entry
    {
        return new Entry("2023-02-06 14:41:15", "warning", "moved", "not found", "Template3 from unknown");
    }

    private function loginFailureEntry(): Entry
    {
        return new Entry("2025-03-07 13:21:17", "warning", "XH", "login", "login failed from ::1");
    }
}
