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

use Logman\Model\Entry;
use Logman\Model\Logfile;
use Plib\Request;
use Plib\View;

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
    public function __invoke(Request $request)
    {
        switch ($request->get("action")) {
            default:
                return $this->show($request);
            case "delete":
                return $request->post("logman_do") !== null ? $this->doDelete($request) : $this->delete($request);
        }
    }

    private function show(Request $request): string
    {
        $filters = $this->activeFilters($request);
        $max = (int) $this->conf["entries_max"];
        if ($max <= 0) {
            $max = PHP_INT_MAX;
        }
        $entries = $this->logfile->find($filters, $max);
        return $this->view->render("admin", [
            "count" => count($entries),
            "deleted" => (int) ($request->get("logman_deleted") ?? -1),
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

    private function delete(Request $request): string
    {
        return $this->view->render("confirm_delete", [
            "count" => $request->get("logman_count") ?? 0,
        ]);
    }

    /** @return never */
    private function doDelete(Request $request)
    {
        $filters = $this->activeFilters($request);
        $count = (int) ($request->get("logman_count") ?? 0);
        $deleted = $this->logfile->delete($count, $filters);
        $this->redirect($request, $deleted);
    }

    /** @return array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} */
    private function activeFilters(Request $request): array
    {
        $filters = [];
        if ($request->get("logman_timestamp") !== null) {
            $filters["timestamp"] = $request->get("logman_timestamp");
        }
        if ($request->get("logman_level") !== null) {
            $filters["level"] = $request->get("logman_level");
        }
        if ($request->get("logman_module") !== null) {
            $filters["module"] = $request->get("logman_module");
        }
        if ($request->get("logman_category") !== null) {
            $filters["category"] = $request->get("logman_category");
        }
        if ($request->get("logman_description") !== null) {
            $filters["description"] = $request->get("logman_description");
        }
        return $filters;
    }

    /** @return never */
    protected function redirect(Request $request, int $deleted)
    {
        $url = $request->url()->without("action")->with("logman_deleted", (string) $deleted);
        header("Location: " . $url->absolute(), true, 303);
        exit;
    }
}
