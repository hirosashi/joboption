<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    /** @var array<int, string> */
    private array $errors = [];

    /** @param array<string, mixed> $data */
    public function __construct(private array $data)
    {
    }

    public function str(string $key): string
    {
        $v = $this->data[$key] ?? '';
        return is_string($v) ? trim($v) : '';
    }

    public function required(string $key, string $label): self
    {
        if ($this->str($key) === '') {
            $this->errors[] = "{$label}を入力してください。";
        }
        return $this;
    }

    public function maxLength(string $key, int $max, string $label): self
    {
        if (mb_strlen($this->str($key)) > $max) {
            $this->errors[] = "{$label}は{$max}文字以内で入力してください。";
        }
        return $this;
    }

    public function number(string $key, string $label): self
    {
        $v = self::numStr($this->str($key));
        if ($v !== '' && !is_numeric($v)) {
            $this->errors[] = "{$label}は数字で入力してください。";
        }
        return $this;
    }

    public function positive(string $key, string $label): self
    {
        $v = self::numStr($this->str($key));
        if ($v !== '' && (!is_numeric($v) || (float)$v < 0)) {
            $this->errors[] = "{$label}は0以上の数字で入力してください。";
        }
        return $this;
    }

    public function date(string $key, string $label): self
    {
        $v = $this->str($key);
        if ($v !== '' && Clock::parse($v) === null) {
            $this->errors[] = "{$label}は日付（例 2025-06-01）で入力してください。";
        }
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<int, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    /** 全角数字・カンマ・「円」「時給」などを取り除いた数値文字列 */
    public static function numStr(string $s): string
    {
        $s = mb_convert_kana($s, 'ns');
        return str_replace([',', '，', ' ', '円', '¥', '￥', '時給', '日給', '月給', '年収'], '', $s);
    }

    /** 数値入力を float|null に。空は null */
    public static function toNum(mixed $v): ?float
    {
        if (!is_string($v) && !is_numeric($v)) {
            return null;
        }
        $s = self::numStr((string)$v);
        if ($s === '' || !is_numeric($s)) {
            return null;
        }
        return (float)$s;
    }

    public static function toDate(mixed $v): ?string
    {
        if (!is_string($v)) {
            return null;
        }
        $d = Clock::parse($v);
        return $d === null ? null : $d->format('Y-m-d');
    }

    public static function toStr(mixed $v): ?string
    {
        if (!is_string($v)) {
            return null;
        }
        $s = trim($v);
        return $s === '' ? null : $s;
    }
}
