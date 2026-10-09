<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use App\Core\Clock;
use App\Core\Db;

/** 求人の検索・表示用の整形 */
final class Jobs
{
    public const PER_PAGE = 20;

    private const SELECT = 'SELECT j.*, c.name AS company_name, c.email AS company_email, c.url AS company_url, k.name AS category_name
        FROM jobs j JOIN companies c ON c.id = j.company_id LEFT JOIN categories k ON k.id = j.category_id';

    /** 公開中かつ掲載期限内 */
    private static function publicWhere(): array
    {
        return ["j.status = 'published' AND (j.valid_until IS NULL OR j.valid_until >= ?)", [Clock::today()]];
    }

    /**
     * @param array{pref?: string, cat?: int, tag?: int, q?: string} $f
     * @return array{rows: array<int, array<string, mixed>>, total: int}
     */
    public static function search(array $f, int $page = 1, int $perPage = self::PER_PAGE): array
    {
        [$where, $params] = self::publicWhere();
        if (($f['pref'] ?? '') !== '') {
            $where .= ' AND j.prefecture = ?';
            $params[] = $f['pref'];
        }
        if (($f['cat'] ?? 0) > 0) {
            $where .= ' AND j.category_id = ?';
            $params[] = $f['cat'];
        }
        if (($f['tag'] ?? 0) > 0) {
            $where .= ' AND EXISTS (SELECT 1 FROM job_tags t WHERE t.job_id = j.id AND t.tag_id = ?)';
            $params[] = $f['tag'];
        }
        $q = trim(mb_convert_kana((string)($f['q'] ?? ''), 's'));
        if ($q !== '') {
            foreach (array_slice(preg_split('/\s+/u', $q) ?: [], 0, 5) as $word) {
                $cols = ['j.title', 'j.job_name', 'j.description', 'j.city', 'j.station', 'c.name'];
                $where .= ' AND (' . implode(' OR ', array_map(static fn(string $c): string => $c . " LIKE ? ESCAPE '!'", $cols)) . ')';
                $like = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $word) . '%';
                array_push($params, $like, $like, $like, $like, $like, $like);
            }
        }
        $total = (int)Db::value('SELECT COUNT(*) FROM jobs j JOIN companies c ON c.id = j.company_id WHERE ' . $where, $params);
        $offset = max(0, ($page - 1) * $perPage);
        $rows = Db::rows(self::SELECT . ' WHERE ' . $where . ' ORDER BY j.published_at DESC, j.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset, $params);
        return ['rows' => self::withTags($rows), 'total' => $total];
    }

    /** @return array<string, mixed>|null */
    public static function findPublic(int $id): ?array
    {
        [$where, $params] = self::publicWhere();
        $params[] = $id;
        $r = Db::row(self::SELECT . ' WHERE ' . $where . ' AND j.id = ?', $params);
        return $r === null ? null : self::withTags([$r])[0];
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        $r = Db::row(self::SELECT . ' WHERE j.id = ?', [$id]);
        return $r === null ? null : self::withTags([$r])[0];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function withTags(array $rows): array
    {
        if ($rows === []) {
            return [];
        }
        $ids = array_map(static fn(array $r): int => (int)$r['id'], $rows);
        $in = implode(',', array_fill(0, count($ids), '?'));
        $map = [];
        foreach (Db::rows("SELECT jt.job_id, t.id, t.name FROM job_tags jt JOIN tags t ON t.id = jt.tag_id WHERE jt.job_id IN ($in) ORDER BY t.sort_no, t.id", $ids) as $t) {
            $map[(int)$t['job_id']][(int)$t['id']] = (string)$t['name'];
        }
        foreach ($rows as &$r) {
            $r['tags'] = $map[(int)$r['id']] ?? [];
        }
        unset($r);
        return $rows;
    }

    /** @param array<string, mixed> $j */
    public static function salary(array $j): string
    {
        $type = Master::SALARY_TYPES[(string)$j['salary_type']] ?? '';
        $min = $j['salary_min'] === null ? null : (int)$j['salary_min'];
        $max = $j['salary_max'] === null ? null : (int)$j['salary_max'];
        if ($min === null && $max === null) {
            return $type . ' 応相談';
        }
        if ($max === null || $max === $min) {
            return $type . ' ' . number_format((int)$min) . '円' . ($max === null ? '〜' : '');
        }
        return $type . ' ' . number_format((int)$min) . '〜' . number_format($max) . '円';
    }

    /** @param array<string, mixed> $j */
    public static function place(array $j): string
    {
        return trim((string)$j['prefecture'] . (string)($j['city'] ?? ''));
    }

    /** Googleしごと検索向け JobPosting 構造化データ @param array<string, mixed> $j */
    public static function jsonLd(array $j): string
    {
        $desc = '';
        foreach (['description' => '仕事内容', 'requirements' => '応募資格', 'benefits' => '待遇・福利厚生'] as $k => $label) {
            if (trim((string)($j[$k] ?? '')) !== '') {
                $desc .= '<p><strong>' . $label . '</strong><br>' . nl2br(htmlspecialchars((string)$j[$k], ENT_QUOTES, 'UTF-8')) . '</p>';
            }
        }
        $site = App::config()['site'] ?? [];
        $data = [
            '@context' => 'https://schema.org/',
            '@type' => 'JobPosting',
            'title' => (string)$j['job_name'],
            'description' => $desc,
            'identifier' => ['@type' => 'PropertyValue', 'name' => (string)($site['name'] ?? 'joboption'), 'value' => (string)$j['id']],
            'datePosted' => substr((string)($j['published_at'] ?? $j['created_at']), 0, 10),
            'employmentType' => Master::EMPLOYMENT_SCHEMA[(string)$j['employment_type']] ?? 'OTHER',
            'hiringOrganization' => array_filter(['@type' => 'Organization', 'name' => (string)$j['company_name'], 'sameAs' => (string)($j['company_url'] ?? '')]),
            'jobLocation' => ['@type' => 'Place', 'address' => array_filter([
                '@type' => 'PostalAddress',
                'addressRegion' => (string)$j['prefecture'],
                'addressLocality' => (string)($j['city'] ?? ''),
                'addressCountry' => 'JP',
            ])],
        ];
        if (!empty($j['valid_until'])) {
            $data['validThrough'] = (string)$j['valid_until'] . 'T23:59';
        }
        if ($j['salary_min'] !== null) {
            $value = ['@type' => 'QuantitativeValue', 'unitText' => Master::SALARY_SCHEMA[(string)$j['salary_type']] ?? 'HOUR'];
            if ($j['salary_max'] !== null && (int)$j['salary_max'] !== (int)$j['salary_min']) {
                $value['minValue'] = (int)$j['salary_min'];
                $value['maxValue'] = (int)$j['salary_max'];
            } else {
                $value['value'] = (int)$j['salary_min'];
            }
            $data['baseSalary'] = ['@type' => 'MonetaryAmount', 'currency' => 'JPY', 'value' => $value];
        }
        return (string)json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    }
}
