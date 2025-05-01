<?php

namespace Logman;

use ApprovalTests\Approvals;
use Logman\Model\Entry;
use Logman\Model\Log;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore;
use Plib\FakeRequest;
use Plib\View;

class MainAdminTest extends TestCase
{
    /** @var array<string,string> */
    private array $conf;

    private DocumentStore $store;

    private View $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["logman"];
        $this->store = new DocumentStore(vfsStream::url("root/"));
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
    }

    private function sut()
    {
        return new MainAdmin(
            $this->conf,
            $this->store,
            $this->view
        );
    }

    public function testDisplaysLogfile(): void
    {
        $log = Log::updateIn($this->store);
        $log->append($this->loginSuccessEntry());
        $log->append($this->movedEntry());
        $log->append($this->loginFailureEntry());
        $this->store->commit();
        $request = new FakeRequest([
            "url" => "http://example.com/?&action=plugin_text",
        ]);
        $response = $this->sut()($request);
        Approvals::verifyHtml($response->output());
    }

    /** <https://github.com/cmb69/logman_xh/issues/1> */
    public function testZeroMaxEntriesDisplaysEntries(): void
    {
        $log = Log::updateIn($this->store);
        $log->append($this->loginSuccessEntry());
        $this->store->commit();
        $this->conf["entries_max"] = "0";
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
        $log = Log::updateIn($this->store);
        $log->append($this->loginSuccessEntry());
        $this->store->commit();
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
        $log = Log::updateIn($this->store);
        $log->append($this->loginSuccessEntry());
        $this->store->commit();
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
