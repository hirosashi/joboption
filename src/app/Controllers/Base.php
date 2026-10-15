<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Router;

abstract class Base
{
    protected static function notFound(): never
    {
        Router::notFound();
        exit;
    }

    /** @return array<string, string> */
    protected static function postStrings(array $keys): array
    {
        $out = [];
        foreach ($keys as $k) {
            $v = $_POST[$k] ?? '';
            $out[$k] = is_string($v) ? trim($v) : '';
        }
        return $out;
    }

    /** スパムよけ（ボットだけが埋める隠し項目） */
    protected static function isBot(): bool
    {
        return trim((string)($_POST['website'] ?? '')) !== '';
    }

    protected static function validTel(string $tel): bool
    {
        return (bool)preg_match('/^[0-9０-９\-－ー()（） +]{10,20}$/u', $tel);
    }

    protected static function normalizeTel(string $tel): string
    {
        return str_replace(['－', 'ー', '（', '）'], ['-', '-', '(', ')'], mb_convert_kana($tel, 'n'));
    }

    protected static function siteName(): string
    {
        return (string)(App::config()['site']['name'] ?? 'お仕事55号');
    }
}
