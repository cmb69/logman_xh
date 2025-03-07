<?php

/**
 * Copyright 2025 Christoph M. Becker
 *
 * This file is part of Logman_XH.
 *
 * Logman_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Logman_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Logman_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Logman;

use Logman\Infra\View;

class PluginInfo
{
    private string $folder;
    private View $view;

    public function __construct(string $folder, View $view)
    {
        $this->folder = $folder;
        $this->view = $view;
    }

    public function __invoke(string $sl): string
    {
        return $this->view->render("info", [
            "version" => LOGMAN_VERSION,
            "checks" => [
                $this->checkXhVersion("1.7.0"),
                $this->checkPhpVersion("7.4.0"),
                $this->checkWritability("{$this->folder}config/config.php"),
                $this->checkWritability("{$this->folder}css/stylesheet.css"),
                $this->checkWritability("{$this->folder}languages/{$sl}.php"),
            ],
        ]);
    }

    /** @return object{class:string,key:string,arg:string,result:string} */
    private function checkXhVersion(string $version): object
    {
        $ok = $this->isXhVersionAtLeast($version);
        return (object) [
            "class" => $ok ? "xh_success" : "xh_fail",
            "key" => $ok ? "syscheck_xh_version" : "syscheck_xh_version_no",
            "arg" => $version,
            "result" => $ok ? "syscheck_good" : "syscheck_bad",
        ];
    }

    /** @return object{class:string,key:string,arg:string,result:string} */
    private function checkPhpVersion(string $version): object
    {
        $ok = $this->isPhpVersionAtLeast($version);
        return (object) [
            "class" => $ok ? "xh_success" : "xh_fail",
            "key" => $ok ? "syscheck_php_version" : "syscheck_php_version_no",
            "arg" => $version,
            "result" => $ok ? "syscheck_good" : "syscheck_bad",
        ];
    }

    /** @return object{class:string,key:string,arg:string,result:string} */
    private function checkWritability(string $filename): object
    {
        $ok = $this->isWritable($filename);
        return (object) [
            "class" => $ok ? "xh_success" : "xh_fail",
            "key" => $ok ? "syscheck_writable" : "syscheck_writable_no",
            "arg" => $filename,
            "result" => $ok ? "syscheck_good" : "syscheck_bad",
        ];
    }

    protected function isXhVersionAtLeast(string $version): bool
    {
        return version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") >= 0;
    }

    protected function isPhpVersionAtLeast(string $version): bool
    {
        return version_compare(PHP_VERSION, $version) >= 0;
    }

    protected function isWritable(string $filename): bool
    {
        return is_writable($filename);
    }
}
