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

class Logfile
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} $filters
     * @return list<Entry>
     */
    public function find(array $filters, int $max = PHP_INT_MAX): array
    {
        $res = [];
        if (($stream = fopen($this->filename, "r"))) {
            flock($stream, LOCK_SH);
            while ($max > 0 && ($line = fgets($stream)) !== false) {
                $record = explode("\t", rtrim($line));
                $entry = new Entry(...$record);
                if ($this->satisfies($entry, $filters)) {
                    $res[] = $entry;
                }
                $max--;
            }
            flock($stream, LOCK_UN);
            fclose($stream);
        }
        return $res;
    }

    /** @param array{timestamp?:string,level?:string,module?:string,category?:string,description?:string} $filters */
    public function delete(int $count, array $filters): int
    {
        $res = 0;
        if (($stream = fopen($this->filename, "r+"))) {
            flock($stream, LOCK_EX);
            if (($temp = fopen("php://memory", "w+"))) {
                while (($line = fgets($stream)) !== false) {
                    $record = explode("\t", rtrim($line));
                    $entry = new Entry(...$record);
                    if ($count > 0 && $this->satisfies($entry, $filters)) {
                        $count--;
                        $res++;
                    } else {
                        if (fwrite($temp, $line) !== strlen($line)) {
                            $res = 0;
                            goto out;
                        }
                    }
                }
                if (
                    !rewind($temp)
                    || !rewind($stream)
                    || !ftruncate($stream, 0)
                    || !stream_copy_to_stream($temp, $stream)
                ) {
                    $res = 0;
                }
                out:
                fclose($temp);
                fflush($stream);
            }
            flock($stream, LOCK_UN);
            fclose($stream);
        }
        return $res;
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
}
