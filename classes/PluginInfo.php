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

use Plib\Request;
use Plib\Response;
use Plib\SystemChecker;
use Plib\View;

class PluginInfo
{
    private string $folder;
    private SystemChecker $systemChecker;
    private View $view;

    public function __construct(string $folder, SystemChecker $systemChecker, View $view)
    {
        $this->folder = $folder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        return Response::create($this->view->render("info", [
            "version" => LOGMAN_VERSION,
            "checks" => [
                $this->checkXhVersion("1.7.0"),
                $this->checkPhpVersion("7.4.0"),
                $this->checkWritability("{$this->folder}config/config.php"),
                $this->checkWritability("{$this->folder}css/stylesheet.css"),
                $this->checkWritability("{$this->folder}languages/{$request->language()}.php"),
            ],
        ]));
    }

    /** @return object{class:string,key:string,arg:string,result:string} */
    private function checkXhVersion(string $version): object
    {
        $ok = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version");
        return (object) [
            "class" => $ok ? "xh_success" : "xh_fail",
            "key" => "syscheck_xh_version",
            "arg" => $version,
            "result" => $ok ? "syscheck_good" : "syscheck_bad",
        ];
    }

    /** @return object{class:string,key:string,arg:string,result:string} */
    private function checkPhpVersion(string $version): object
    {
        $ok = $this->systemChecker->checkVersion(PHP_VERSION, $version);
        return (object) [
            "class" => $ok ? "xh_success" : "xh_fail",
            "key" => "syscheck_php_version",
            "arg" => $version,
            "result" => $ok ? "syscheck_good" : "syscheck_bad",
        ];
    }

    /** @return object{class:string,key:string,arg:string,result:string} */
    private function checkWritability(string $filename): object
    {
        $ok = $this->systemChecker->checkWritability($filename);
        return (object) [
            "class" => $ok ? "xh_success" : "xh_fail",
            "key" => "syscheck_writable",
            "arg" => $filename,
            "result" => $ok ? "syscheck_good" : "syscheck_bad",
        ];
    }
}
