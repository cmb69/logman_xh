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
use Logman\Model\Entry;
use Logman\Model\Logfile;

use function strlen;

class MainAdmin
{
    /** @var Logfile */
    private $logfile;

    /** @var View */
    private $view;

    public function __construct(Logfile $logfile, View $view)
    {
        $this->logfile = $logfile;
        $this->view = $view;
    }

    public function __invoke(): string
    {
        $filters = [];
        if (!empty($_GET["logman_timestamp"]) && is_string($_GET["logman_timestamp"])) {
            $filters["timestamp"] = $_GET["logman_timestamp"];
        }
        if (!empty($_GET["logman_level"]) && is_string($_GET["logman_level"])) {
            $filters["level"] = $_GET["logman_level"];
        }
        if (!empty($_GET["logman_module"]) && is_string($_GET["logman_module"])) {
            $filters["module"] = $_GET["logman_module"];
        }
        if (!empty($_GET["logman_category"]) && is_string($_GET["logman_category"])) {
            $filters["category"] = $_GET["logman_category"];
        }
        if (!empty($_GET["logman_description"]) && is_string($_GET["logman_description"])) {
            $filters["description"] = $_GET["logman_description"];
        }
        $entries = $this->logfile->find($filters);
        return $this->view->render("admin", [
            "timestamp" => $filters["timestamp"] ?? "",
            "level" => $filters["level"] ?? "",
            "module" => $filters["module"] ?? "",
            "category" => $filters["category"] ?? "",
            "description" => $filters["description"] ?? "",
            "months" => $this->months($entries),
            "levels" => $this->levels($entries),
            "modules" => $this->modules($entries),
            "categories" => $this->categories($entries),
            "entries" => $entries,
        ]);
    }

    /**
     * @param list<Entry> $entries
     * @return list<string>
     */
    private function months(array $entries): array
    {
        $res = [];
        foreach ($entries as $entry) {
            if (!isset($res[$entry->timestamp])) {
                $res[substr($entry->timestamp, 0, strlen("YYYY-MM"))] = true;
            }
        }
        return array_keys($res);
    }

    /**
     * @param list<Entry> $entries
     * @return list<string>
     */
    private function levels(array $entries): array
    {
        $res = [];
        foreach ($entries as $entry) {
            if (!isset($res[$entry->level])) {
                $res[$entry->level] = true;
            }
        }
        return array_keys($res);
    }

    /**
     * @param list<Entry> $entries
     * @return list<string>
     */
    private function modules(array $entries): array
    {
        $res = [];
        foreach ($entries as $entry) {
            if (!isset($res[$entry->module])) {
                $res[$entry->module] = true;
            }
        }
        return array_keys($res);
    }

    /**
     * @param list<Entry> $entries
     * @return list<string>
     */
    private function categories(array $entries): array
    {
        $res = [];
        foreach ($entries as $entry) {
            if (!isset($res[$entry->category])) {
                $res[$entry->category] = true;
            }
        }
        return array_keys($res);
    }
}
