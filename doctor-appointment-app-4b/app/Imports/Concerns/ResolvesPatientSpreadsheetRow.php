<?php

namespace App\Imports\Concerns;

use Illuminate\Support\Str;

trait ResolvesPatientSpreadsheetRow
{
    /**
     * @param  array<int|string, mixed>  $row
     * @return array<int|string, mixed>
     */
    private function mergeHeadingAliases(array $row): array
    {
        $extra = [];
        foreach ($row as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            $clean = preg_replace('/^\xEF\xBB\xBF/u', '', $key);
            $slug = Str::slug($clean, '_');
            if ($slug !== '' && ! array_key_exists($slug, $row)) {
                $extra[$slug] = $value;
            }
            $lower = strtolower(trim($clean));
            if ($lower !== '' && ! array_key_exists($lower, $row)) {
                $extra[$lower] = $value;
            }
        }

        return $row + $extra;
    }

    /**
     * @param  array<int|string, mixed>  $row
     */
    private function firstByHeaderSubstring(array $row, array $needles, array $excludeNeedles): ?string
    {
        foreach ($row as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            $slug = Str::slug(preg_replace('/^\xEF\xBB\xBF/u', '', $key), '_');
            foreach ($excludeNeedles as $ex) {
                if (str_contains($slug, $ex)) {
                    continue 2;
                }
            }
            foreach ($needles as $needle) {
                if (str_contains($slug, $needle)) {
                    $s = $this->cellString($value);
                    if ($s !== null) {
                        return $s;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int|string, mixed>  $row
     */
    private function guessEmailFromRow(array $row): ?string
    {
        foreach ($row as $value) {
            $s = $this->cellString($value);
            if ($s !== null && str_contains($s, '@') && filter_var($s, FILTER_VALIDATE_EMAIL)) {
                return $s;
            }
        }

        return null;
    }

    private function cellString(mixed $v): ?string
    {
        if ($v === null) {
            return null;
        }
        if (is_string($v)) {
            $s = trim($v);

            return $s === '' ? null : $s;
        }
        if (is_int($v)) {
            return (string) $v;
        }
        if (is_float($v)) {
            if (is_finite($v) && abs($v - round($v)) < 1e-9) {
                return number_format($v, 0, '.', '');
            }

            $s = trim((string) $v);

            return $s === '' ? null : $s;
        }
        if ($v instanceof \DateTimeInterface) {
            return $v->format('Y-m-d');
        }
        if (is_object($v) && method_exists($v, '__toString')) {
            $s = trim((string) $v);

            return $s === '' ? null : $s;
        }

        return null;
    }

    /**
     * @param  array<int|string, mixed>  $row
     */
    private function first(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $row)) {
                continue;
            }
            $s = $this->cellString($row[$key]);
            if ($s !== null) {
                return $s;
            }
        }

        return null;
    }

    /**
     * @param  array<int, string|null>  $parts
     */
    private function joinNameParts(array $parts): ?string
    {
        $clean = [];
        foreach ($parts as $p) {
            if ($p !== null && trim($p) !== '') {
                $clean[] = trim($p);
            }
        }

        return $clean === [] ? null : implode(' ', $clean);
    }
}
