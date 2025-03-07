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
    private $sut;

    /** @var Logfile&MockObject */
    private $logfile;

    public function customSetUp(?int $maxEntries = null): void
    {
        $conf = XH_includeVar("./config/config.php", "plugin_cf")["logman"];
        if ($maxEntries !== null) {
            $conf["entries_max"] = (string) $maxEntries;
        }
        $this->logfile = $this->createMock(Logfile::class);
        $this->logfile->expects($this->any())->method("find")->willReturnCallback(function (array $filters, int $max) {
            if ($max === 0) {
                return [];
            } else {
                return [new Entry("2023-01-30 14:00:05", "info", "XH", "login", "login from ::1")];
            }
        });
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["logman"]);
        $this->sut = $this->getMockBuilder(MainAdmin::class)
            ->setConstructorArgs([$conf, $this->logfile, $view])
            ->onlyMethods(["redirect"])
            ->getMock();
    }

    public function testDisplaysLogfile(): void
    {
        $this->customSetUp();
        $_GET = [
            "action" => "plugin_text",
            "logman_timestamp" => "2025",
            "logman_level" => "info",
            "logman_module" => "XH",
            "logman_category" => "login",
            "logman_description" => "from"
        ];
        Approvals::verifyHtml(($this->sut)());
    }

    /** <https://github.com/cmb69/logman_xh/issues/1> */
    public function testZeroMaxEntriesDisplaysEntries(): void
    {
        $this->customSetUp(0);
        $_GET = [
            "action" => "plugin_text",
        ];
        $this->assertStringContainsString("2023-01-30 14:00:05", ($this->sut)());
    }

    public function testDisplaysDeleteConfirmation(): void
    {
        $this->customSetUp();
        $_GET = [
            "action" => "delete",
            "logman_count" => "1",
            "logman_timestamp" => "2025",
            "logman_level" => "info",
            "logman_module" => "XH",
            "logman_category" => "login",
            "logman_description" => "from"
        ];
        Approvals::verifyHtml(($this->sut)());
    }

    public function testDeletesEntries(): void
    {
        $this->customSetUp();
        $this->logfile->expects($this->once())->method("delete")->willReturn(1);
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
        $this->sut->expects($this->once())->method("redirect");
        ($this->sut)();
    }

    public function testDisplaysNumberOfDeletedEntries(): void
    {
        $this->customSetUp();
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
        $this->assertStringContainsString("17 entries deleted!", ($this->sut)());
    }
}
