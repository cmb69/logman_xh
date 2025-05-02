<?php

/**
 * Copyright (c) Christoph M. Becker
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

use Plib\Document;
use Plib\DocumentStore;

final class Log implements Document
{
    /** @var list<Entry> */
    private array $entries;

    public static function retrieveFrom(DocumentStore $store): self
    {
        $that = $store->retrieve("log.txt", self::class);
        assert($that instanceof self);
        return $that;
    }

    public static function updateIn(DocumentStore $store): self
    {
        $that = $store->update("log.txt", self::class);
        assert($that instanceof self);
        return $that;
    }

    public static function fromString(string $contents, string $key): self
    {
        $that = new self([]);
        if (($lines = preg_split('/(?:\r)?\n/', $contents)) === false) {
            return $that;
        }
        foreach ($lines as $line) {
            if ($line === "") {
                continue;
            }
            $record = array_pad(explode("\t", $line, 5), 5, "");
            $that->entries[] = new Entry(...$record);
        }
        return $that;
    }

    /** @param list<Entry> $entries */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    public function append(Entry $entry): void
    {
        $this->entries[] = $entry;
    }

    /** @return list<string> */
    public function months(): array
    {
        $res = [];
        foreach ($this->entries as $entry) {
            if (!isset($res[$entry->timestamp])) {
                $res[substr($entry->timestamp, 0, strlen("YYYY-MM"))] = true;
            }
        }
        return array_keys($res);
    }

    /** @return list<string> */
    public function levels(): array
    {
        $res = [];
        foreach ($this->entries as $entry) {
            if (!isset($res[$entry->level])) {
                $res[$entry->level] = true;
            }
        }
        return array_keys($res);
    }

    /** @return list<string> */
    public function modules(): array
    {
        $res = [];
        foreach ($this->entries as $entry) {
            if (!isset($res[$entry->module])) {
                $res[$entry->module] = true;
            }
        }
        return array_keys($res);
    }

    /** @return list<string> */
    public function categories(): array
    {
        $res = [];
        foreach ($this->entries as $entry) {
            if (!isset($res[$entry->category])) {
                $res[$entry->category] = true;
            }
        }
        return array_keys($res);
    }

    /**
     * @param array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} $filters
     * @return list<Entry>
     */
    public function filter(array $filters, bool $ascending = true, int $max = PHP_INT_MAX): array
    {
        $res = [];
        $entries = $ascending ? $this->entries : array_reverse($this->entries);
        foreach ($entries as $entry) {
            if ($max-- <= 0) {
                break;
            }
            if ($this->satisfies($entry, $filters, $ascending ? 1 : -1)) {
                $res[] = $entry;
            }
        }
        return $res;
    }

    /** @param array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} $filters */
    public function delete(array $filters, int $max): int
    {
        $count = 0;
        foreach ($this->entries as $index => $entry) {
            if ($max-- <= 0) {
                break;
            }
            if ($this->satisfies($entry, $filters)) {
                unset($this->entries[$index]);
                $count++;
            }
        }
        return $count;
    }

    /** @param array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} $filters */
    private function satisfies(Entry $entry, array $filters, int $order = 1): bool
    {
        return (!isset($filters["timestamp"]) || strcmp($entry->timestamp, $filters["timestamp"]) * $order < 0)
            && (!isset($filters["level"]) || $entry->level === $filters["level"])
            && (!isset($filters["module"]) || $entry->module === $filters["module"])
            && (!isset($filters["category"]) || $entry->category === $filters["category"])
            && (!isset($filters["description"]) || strpos($entry->description, $filters["description"]) !== false);
    }

    public function toString(): string
    {
        $lines = [];
        foreach ($this->entries as $entry) {
            $lines[] = $entry->toString();
        }
        return implode("\n", $lines);
    }
}
