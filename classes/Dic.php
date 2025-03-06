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
use Logman\Model\Logfile;

class Dic
{
    public static function makeMainAdmin(): MainAdmin
    {
        global $pth, $plugin_cf, $plugin_tx;

        $logfile = new Logfile($pth["file"]["log"]);
        $view = new View($pth["folder"]["plugins"] . "logman/views/", $plugin_tx["logman"]);
        return new MainAdmin($plugin_cf["logman"], $logfile, $view);
    }
}
