<?php

namespace Logman\Model;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class LogfileTest extends TestCase
{
    private $sut;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $filename = vfsStream::url("root/log.txt");
        file_put_contents($filename, [
            "2023-02-09 16:14:45	warning	moved	not found	wurst from unknown\n",
            "2025-03-04 16:25:35	info	XH	login	login from ::1\n",
        ]);
        $this->sut = new Logfile($filename);
    }

    public function testFindsAllEntries(): void
    {
        $this->assertEquals([
            new Entry("2023-02-09 16:14:45", "warning", "moved", "not found", "wurst from unknown"),
            new Entry("2025-03-04 16:25:35", "info", "XH", "login", "login from ::1"),
        ], $this->sut->find([]));
    }

    public function testFindsFilteredEntries(): void
    {
        $this->assertEquals([
            new Entry("2025-03-04 16:25:35", "info", "XH", "login", "login from ::1"),
        ], $this->sut->find(["module" => "XH", "category" => "login", "description" => "::1"]));
    }

    public function testDeletesEntries(): void
    {
        $this->assertEquals(1, $this->sut->delete(1, []));
        $this->assertEquals([
            new Entry("2025-03-04 16:25:35", "info", "XH", "login", "login from ::1"),
        ], $this->sut->find([]));
    }
}
