<?php

namespace Logman;

use ApprovalTests\Approvals;
use Logman\Model\Entry;
use Logman\Model\Logfile;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class MainAdminTest extends TestCase
{
    /** @var array<string,string> */
    private array $conf;

    /** @var LogFile&MockObject */
    private $logfile;

    private View $view;

    public function setUp(): void
    {
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["logman"];
        $this->logfile = $this->createStub(Logfile::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
    }

    private function sut()
    {
        return new MainAdmin($this->conf, $this->logfile, $this->view);
    }

    public function testDisplaysLogfile(): void
    {
        $this->logfile->expects($this->once())->method("find")->willReturn([
            $this->loginSuccessEntry(),
            $this->movedEntry(),
            $this->loginFailureEntry(),
        ]);
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=plugin_text&logman_timestamp=2025&logman_level=info&logman_module=XH"
                . "&logman_category=login&logman_description=from",
        ]);
        $response = $this->sut()($request);
        Approvals::verifyHtml($response->output());
    }

    /** <https://github.com/cmb69/logman_xh/issues/1> */
    public function testZeroMaxEntriesDisplaysEntries(): void
    {
        $this->conf["entries_max"] = "0";
        $this->logfile->expects($this->once())->method("find")->with($this->anything(), PHP_INT_MAX)
            ->willReturn([$this->loginSuccessEntry()]);
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=plugin_text",
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("2023-01-30 14:00:05", $response->output());
    }

    public function testDisplaysDeleteConfirmation(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=delete&logman_count=1",
        ]);
        $response = $this->sut()($request);
        Approvals::verifyHtml($response->output());
    }

    public function testDeletesEntries(): void
    {
        $this->logfile->expects($this->once())->method("delete")->willReturn(1);
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=delete&logman_count=1",
            "post" => [
                "logman_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertSame("http://example.com/?&logman_count=1&logman_deleted=1", $response->location());
    }

    public function testDisplaysNumberOfDeletedEntries(): void
    {
        $this->logfile->expects($this->once())->method("find")->willReturn([$this->loginSuccessEntry()]);
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=&logman_deleted=17",
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("17 entries deleted!", $response->output());
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
