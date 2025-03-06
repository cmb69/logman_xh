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

namespace Logman\Infra;

class View
{
    /** @var string $folder */
    private $folder;

    /** @var array<string,string> $lang */
    private $lang;

    /** @param array<string,string> $lang */
    public function __construct(string $folder, array $lang)
    {
        $this->folder = $folder;
        $this->lang = $lang;
    }

    /** @param array<string,mixed> $_data */
    public function render(string $_template, array $_data): string
    {
        extract($_data);
        ob_start();
        include "{$this->folder}{$_template}.php";
        return (string) ob_get_clean();
    }


    /** @param scalar $args */
    protected function text(string $key, ...$args): string
    {
        return sprintf($this->esc($this->lang[$key]), ...$args);
    }

    /** @param scalar $args */
    protected function plural(string $key, int $count, ...$args): string
    {
        $suffix = ($count <= 0) ? "_5" : XH_numberSuffix($count);
        return sprintf($this->esc($this->lang["{$key}{$suffix}"]), $count, ...$args);
    }

    protected function esc(string $string): string
    {
        return XH_hsc($string);
    }
}
