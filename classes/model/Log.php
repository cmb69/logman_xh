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
    private array $entries = [];

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
        $that = new self();
        if (($lines = preg_split('/(?:\r)?\n/', $contents)) === false) {
            return $that;
        }
        foreach ($lines as $line) {
            if ($line === "") {
                continue;
            }
            $record = explode("\t", rtrim($line));
            $that->entries[] = new Entry(...$record);
        }
        return $that;
    }

    public function append(Entry $entry): void
    {
        $this->entries[] = $entry;
    }

    /**
     * @param array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} $filters
     * @return list<Entry>
     */
    public function filter(array $filters, int $max = PHP_INT_MAX): array
    {
        $res = [];
        foreach ($this->entries as $entry) {
            if ($max-- <= 0) {
                break;
            }
            if ($this->satisfies($entry, $filters)) {
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
    private function satisfies(Entry $entry, array $filters): bool
    {
        return (!isset($filters["timestamp"]) || strcmp($entry->timestamp, $filters["timestamp"]) < 0)
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
