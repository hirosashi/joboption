<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\App;

/** 求人の写真（3枚まで）。uploads/jobs/ に JPEG で保存し、jobs.image1〜3 にファイル名を持つ */
final class JobImages
{
    public const SLOTS = ['image1', 'image2', 'image3'];
    private const MAX_PX = 1600;
    private const MAX_BYTES = 10 * 1024 * 1024;

    public static function dir(): string
    {
        return dirname(__DIR__, 2) . '/uploads/jobs';
    }

    public static function url(string $name): string
    {
        return App::url('/uploads/jobs/' . rawurlencode($name));
    }

    /** アップロードの検査。問題なければ null */
    public static function check(mixed $file, string $label): ?string
    {
        $err = is_array($file) ? (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;
        if ($err === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE || (int)($file['size'] ?? 0) > self::MAX_BYTES) {
            return "{$label}のファイルが大きすぎます。";
        }
        if ($err !== UPLOAD_ERR_OK || !is_uploaded_file((string)$file['tmp_name'])) {
            return "{$label}をアップロードできませんでした。";
        }
        $info = @getimagesize((string)$file['tmp_name']);
        if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return "{$label}は JPEG・PNG・WebP の画像を選んでください。";
        }
        return null;
    }

    /** 向きを直して縮小し、JPEG で保存する（撮影場所などの情報は残さない）。保存したファイル名を返す */
    public static function store(string $tmp): string
    {
        $type = (int)(getimagesize($tmp)[2] ?? 0);
        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($tmp),
            IMAGETYPE_PNG => imagecreatefrompng($tmp),
            IMAGETYPE_WEBP => imagecreatefromwebp($tmp),
            default => false,
        };
        if ($src === false) {
            throw new \RuntimeException('画像を読み込めませんでした');
        }
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $angle = [3 => 180, 6 => -90, 8 => 90][(int)(@exif_read_data($tmp)['Orientation'] ?? 1)] ?? 0;
            if ($angle !== 0) {
                $src = imagerotate($src, $angle, 0) ?: $src;
            }
        }
        $w = imagesx($src);
        $h = imagesy($src);
        $r = min(1, self::MAX_PX / max($w, $h));
        $nw = max(1, (int)round($w * $r));
        $nh = max(1, (int)round($h * $r));
        $dst = imagecreatetruecolor($nw, $nh);
        imagefill($dst, 0, 0, (int)imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        if (!is_dir(self::dir())) {
            mkdir(self::dir(), 0755, true);
        }
        $name = bin2hex(random_bytes(12)) . '.jpg';
        if (!imagejpeg($dst, self::dir() . '/' . $name, 85)) {
            throw new \RuntimeException('画像を保存できませんでした');
        }
        return $name;
    }

    /** 求人のコピー用に写真ファイルを複製する */
    public static function duplicate(mixed $name): ?string
    {
        if (!self::valid($name) || !is_file(self::dir() . '/' . $name)) {
            return null;
        }
        $new = bin2hex(random_bytes(12)) . '.jpg';
        return copy(self::dir() . '/' . $name, self::dir() . '/' . $new) ? $new : null;
    }

    public static function remove(mixed $name): void
    {
        if (self::valid($name) && is_file(self::dir() . '/' . $name)) {
            unlink(self::dir() . '/' . $name);
        }
    }

    private static function valid(mixed $name): bool
    {
        return is_string($name) && preg_match('/^[0-9a-f]{24}\.jpg$/', $name) === 1;
    }
}
