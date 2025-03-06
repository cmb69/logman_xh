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
    /** @var array<string,string> */
    private array $conf;

    /** @var Logfile */
    private $logfile;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(array $conf, Logfile $logfile, View $view)
    {
        $this->conf = $conf;
        $this->logfile = $logfile;
        $this->view = $view;
    }

    /** @return string|never */
    public function __invoke()
    {
        switch ($_GET["action"] ?? "") {
            default:
                return $this->show();
            case "delete":
                return isset($_POST["logman_do"]) ? $this->doDelete() : $this->delete();
        }
    }

    private function show(): string
    {
        $filters = $this->activeFilters();
        $max = (int) $this->conf["entries_max"];
        if ($max <= 0) {
            $max = PHP_INT_MAX;
        }
        $entries = $this->logfile->find($filters, (int) $this->conf["entries_max"]);
        return $this->view->render("admin", [
            "count" => count($entries),
            "deleted" => (int) ($_GET["logman_deleted"] ?? -1),
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

    private function delete(): string
    {
        return $this->view->render("confirm_delete", [
            "count" => $_GET["logman_count"] ?? 0,
        ]);
    }

    /** @return never */
    private function doDelete()
    {
        $filters = $this->activeFilters();
        $count = (int) ($_GET["logman_count"] ?? 0);
        $deleted = $this->logfile->delete($count, $filters);
        $this->redirect($deleted);
    }

    /** @return array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} */
    private function activeFilters(): array
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
        return $filters;
    }

    /** @return never */
    protected function redirect(int $deleted)
    {
        $query = $_SERVER["QUERY_STRING"];
        $query .= "&logman_deleted=$deleted";
        $query = preg_replace('/action=[^&]*/', "action=", $query);
        header("Location: " . \CMSIMPLE_URL . "?$query", true, 303);
        exit;
    }
}
