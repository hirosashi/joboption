<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Db;

/** 固定の選択肢と、DBのマスタ（職種・特徴タグ）の取得 */
final class Master
{
    public const EMPLOYMENT_TYPES = [
        'part' => 'パート・アルバイト',
        'contract' => '契約社員',
        'full' => '正社員',
        'temp' => '派遣',
        'outsource' => '業務委託',
    ];

    /** Googleしごと検索（JobPosting.employmentType）用 */
    public const EMPLOYMENT_SCHEMA = [
        'part' => 'PART_TIME',
        'contract' => 'TEMPORARY',
        'full' => 'FULL_TIME',
        'temp' => 'TEMPORARY',
        'outsource' => 'CONTRACTOR',
    ];

    public const SALARY_TYPES = ['hour' => '時給', 'day' => '日給', 'month' => '月給', 'year' => '年俸'];
    public const SALARY_SCHEMA = ['hour' => 'HOUR', 'day' => 'DAY', 'month' => 'MONTH', 'year' => 'YEAR'];

    public const JOB_STATUS = ['draft' => '下書き', 'published' => '公開中', 'closed' => '掲載終了'];
    public const APP_STATUS = ['new' => '未対応', 'contacted' => '連絡済み', 'closed' => '完了'];
    public const REQUEST_STATUS = ['new' => '未対応', 'done' => '対応済み'];

    public const PREFECTURES = [
        '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
        '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
        '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県',
        '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
        '鳥取県', '島根県', '岡山県', '広島県', '山口県',
        '徳島県', '香川県', '愛媛県', '高知県',
        '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県',
    ];

    /** @return array<int, string> id => name */
    public static function categories(): array
    {
        return self::pairs('SELECT id, name FROM categories ORDER BY sort_no, id');
    }

    /** @return array<int, string> id => name */
    public static function tags(): array
    {
        return self::pairs('SELECT id, name FROM tags ORDER BY sort_no, id');
    }

    /** 掲載中の求人がある都道府県（検索の選択肢用） @return array<int, string> */
    public static function activePrefectures(): array
    {
        $rows = Db::rows("SELECT DISTINCT prefecture FROM jobs WHERE status = 'published'");
        $have = array_column($rows, 'prefecture');
        return array_values(array_filter(self::PREFECTURES, static fn(string $p): bool => in_array($p, $have, true)));
    }

    /** @return array<int, string> */
    private static function pairs(string $sql): array
    {
        $out = [];
        foreach (Db::rows($sql) as $r) {
            $out[(int)$r['id']] = (string)$r['name'];
        }
        return $out;
    }
}
