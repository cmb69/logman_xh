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

namespace Logman\Model;

class Entry
{
    public string $timestamp;

    public string $level;

    public string $module;

    public string $category;

    public string $description;

    public function __construct(
        string $timestamp,
        string $level,
        string $module,
        string $category,
        string $description
    ) {
        $this->timestamp = $timestamp;
        $this->level = $level;
        $this->module = $module;
        $this->category = $category;
        $this->description = $description;
    }
}
