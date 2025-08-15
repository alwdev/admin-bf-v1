<?php
if (! function_exists('short_middle')) {
    // ย่อกลางสตริง เช่น 0x1234567890…ABCDEF12
    function short_middle(?string $text, int $left = 10, int $right = 8): string {
        if (!$text) return '-';
        $len = mb_strlen($text);
        return ($len <= $left + $right + 1)
            ? $text
            : mb_substr($text, 0, $left).'…'.mb_substr($text, -$right);
    }
}
