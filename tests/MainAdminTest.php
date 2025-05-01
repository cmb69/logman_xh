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
        $response = $sut($request);
        Approvals::verifyHtml($response->output());
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
        $response = $sut($request);
        $this->assertStringContainsString("2023-01-30 14:00:05", $response->output());
    }

    public function testDisplaysDeleteConfirmation(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=delete&logman_count=1",
        ]);
        $response = $sut($request);
        Approvals::verifyHtml($response->output());
    }

    public function testDeletesEntries(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("delete")->willReturn(1);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=delete&logman_count=1",
            "post" => [
                "logman_do" => "",
            ],
        ]);
        $response = $sut($request);
        $this->assertSame("http://example.com/?&logman_count=1&logman_deleted=1", $response->location());
    }

    public function testDisplaysNumberOfDeletedEntries(): void
    {
        $logfile = $this->createMock(Logfile::class);
        $logfile->expects($this->once())->method("find")->willReturn([$this->loginSuccessEntry()]);
        $sut = $this->sut($this->conf(), $logfile, $this->view());
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=&logman_deleted=17",
        ]);
        $response = $sut($request);
        $this->assertStringContainsString("17 entries deleted!", $response->output());
    }

    private function sut(array $conf, Logfile $logfile, View $view)
    {
        return new MainAdmin($conf, $logfile, $view);
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
