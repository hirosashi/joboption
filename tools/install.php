<?php
declare(strict_types=1);
/*
 * 初期セットアップ（CLI専用）: テーブル作成 → マスタ・サンプル求人・管理者を投入（空のときだけ）
 *   php tools/install.php <src のパス> <admin の初期パスワード>
 * 例) php tools/install.php src 'xxxxxxxx'
 */
use App\Core\Clock;
use App\Core\Db;

if (PHP_SAPI !== 'cli') {
    exit(1);
}
$src = rtrim($argv[1] ?? '', '/');
$pw = (string)($argv[2] ?? '');
if ($src === '' || !is_file($src . '/app/bootstrap.php') || strlen($pw) < 8) {
    fwrite(STDERR, "usage: php tools/install.php <src> <admin password (8+ chars)>\n");
    exit(1);
}
require $src . '/app/bootstrap.php';

$schema = __DIR__ . '/../db/' . (Db::driver() === 'sqlite' ? 'schema.sqlite.sql' : 'schema.sql');
if (Db::driver() === 'sqlite') {
    $dir = dirname((string)App\Core\App::config()['db']['path']);
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }
}
Db::conn()->exec((string)file_get_contents($schema));
echo "schema ok\n";

$now = Clock::nowStr();

if ((int)Db::value('SELECT COUNT(*) FROM admins') === 0) {
    Db::exec('INSERT INTO admins (login_id, name, password_hash, created_at, updated_at) VALUES (?,?,?,?,?)', ['admin', '運営管理者', password_hash($pw, PASSWORD_DEFAULT), $now, $now]);
    echo "admin created\n";
}

if ((int)Db::value('SELECT COUNT(*) FROM categories') === 0) {
    $cats = ['事務・受付', '販売・接客', '調理・飲食', '清掃・設備管理', '警備・施設管理', '介護・福祉', '農業・園芸', '製造・軽作業', '配送・ドライバー', 'その他'];
    foreach ($cats as $i => $c) {
        Db::exec('INSERT INTO categories (name, sort_no) VALUES (?,?)', [$c, ($i + 1) * 10]);
    }
    $tags = ['シニア活躍中', '週2〜3日OK', '短時間OK', '未経験OK', '車通勤OK', '駅近', '土日休み', '交通費支給', '制服貸与', '扶養内OK'];
    foreach ($tags as $i => $t) {
        Db::exec('INSERT INTO tags (name, sort_no) VALUES (?,?)', [$t, ($i + 1) * 10]);
    }
    echo "masters ok\n";
}

if ((int)Db::value('SELECT COUNT(*) FROM jobs') === 0) {
    $cat = static fn(string $n): int => (int)Db::value('SELECT id FROM categories WHERE name = ?', [$n]);
    $tag = static fn(string $n): int => (int)Db::value('SELECT id FROM tags WHERE name = ?', [$n]);
    $until = Clock::now()->modify('+90 days')->format('Y-m-d');
    $samples = [
        [
            'company' => ['name' => '【サンプル】さくら住宅管理株式会社', 'contact_name' => '採用担当 山田', 'tel' => '03-0000-0001', 'address' => '東京都世田谷区（サンプル）'],
            'job' => [
                'category' => '清掃・設備管理', 'title' => 'マンション管理員｜週3日・日勤のみ・残業なし', 'job_name' => 'マンション管理員',
                'employment_type' => 'contract', 'salary_type' => 'hour', 'salary_min' => 1350, 'salary_max' => null, 'salary_note' => '交通費別途支給（月2万円まで）',
                'prefecture' => '東京都', 'city' => '世田谷区', 'address' => '東京都世田谷区（勤務先マンションは面接時にご案内）', 'station' => '東急田園都市線「三軒茶屋駅」徒歩7分',
                'work_days' => '週3日（月・水・金）', 'work_hours' => '9:00〜16:00（休憩60分）', 'holidays' => '火・木・土・日・祝日、年末年始',
                'catch_copy' => '定年後の方も多数活躍中。住民の方との挨拶が中心の、落ち着いたお仕事です。',
                'description' => "分譲マンション（約60戸）の管理員として、建物の見守りをお願いします。\n・受付、来訪者・業者の対応\n・共用部の巡回、簡単な清掃\n・ゴミ置き場の整理\n・管理会社への報告（タブレットで簡単に入力できます）",
                'requirements' => "未経験の方も歓迎します。\n・普通に会話ができ、丁寧に対応できる方\n・タブレットの操作は研修でお教えします",
                'benefits' => "交通費支給（月2万円まで）／制服貸与／研修あり（3日間）／有給休暇あり",
                'trial_period' => 'あり（2か月・条件の変更なし）', 'insurance' => '雇用保険・労災保険', 'smoking' => '屋内禁煙',
                'change_scope' => '就業場所：当社の管理するマンション（変更の範囲：東京都内の当社管理物件）／業務：マンション管理業務（変更なし）',
                'contract_period' => '1年ごとの更新（更新上限なし）', 'apply_tel' => '03-0000-0001',
                'tags' => ['シニア活躍中', '週2〜3日OK', '未経験OK', '駅近', '交通費支給', '制服貸与'],
            ],
        ],
        [
            'company' => ['name' => '【サンプル】みのり給食サービス株式会社', 'contact_name' => '採用担当 佐藤', 'tel' => '06-0000-0002', 'address' => '大阪府吹田市（サンプル）'],
            'job' => [
                'category' => '調理・飲食', 'title' => '社員食堂の調理補助｜朝の4時間だけ・土日休み', 'job_name' => '調理補助',
                'employment_type' => 'part', 'salary_type' => 'hour', 'salary_min' => 1250, 'salary_max' => 1300, 'salary_note' => '経験により決定／交通費全額支給',
                'prefecture' => '大阪府', 'city' => '吹田市', 'address' => '大阪府吹田市（企業内の社員食堂）', 'station' => '阪急千里線「関大前駅」徒歩10分',
                'work_days' => '週3〜5日（相談可）', 'work_hours' => '8:00〜12:00', 'holidays' => '土・日・祝日（会社カレンダーによる）',
                'catch_copy' => '午前中だけの短時間勤務。家庭料理の経験がそのまま活かせます。',
                'description' => "企業の社員食堂で、ランチの準備をお手伝いいただきます。\n・野菜の下ごしらえ（皮むき・カット）\n・盛り付け、配膳\n・食器洗浄、片付け\n※1日約150食。チームで分担するので無理なく働けます。",
                'requirements' => "未経験の方も歓迎します（家庭での料理経験があれば十分です）。",
                'benefits' => "交通費全額支給／制服・靴貸与／まかないあり（1食100円）",
                'trial_period' => 'あり（1か月・条件の変更なし）', 'insurance' => '労災保険（勤務日数により雇用保険）', 'smoking' => '敷地内禁煙',
                'change_scope' => '就業場所：吹田市内の受託食堂（変更なし）／業務：調理補助業務（変更なし）',
                'contract_period' => '6か月ごとの更新（更新上限なし）', 'apply_tel' => '06-0000-0002',
                'tags' => ['シニア活躍中', '短時間OK', '未経験OK', '土日休み', '交通費支給', '扶養内OK'],
            ],
        ],
        [
            'company' => ['name' => '【サンプル】あおぞら農園', 'contact_name' => '園主 鈴木', 'tel' => '028-000-0003', 'address' => '栃木県宇都宮市（サンプル）'],
            'job' => [
                'category' => '農業・園芸', 'title' => '野菜の収穫・袋詰め｜週2日から・朝のみ・車通勤OK', 'job_name' => '農作業スタッフ',
                'employment_type' => 'part', 'salary_type' => 'hour', 'salary_min' => 1150, 'salary_max' => null, 'salary_note' => '収穫した野菜のおすそ分けあり',
                'prefecture' => '栃木県', 'city' => '宇都宮市', 'address' => '栃木県宇都宮市（農園）', 'station' => '車・バイク・自転車通勤OK（無料駐車場あり）',
                'work_days' => '週2日〜（曜日は相談できます）', 'work_hours' => '7:00〜11:00（季節により変動）', 'holidays' => 'シフト制（雨天時は休みになる場合あり）',
                'catch_copy' => '畑で体を動かす、気持ちのいい朝のお仕事。農業未経験の方がほとんどです。',
                'description' => "季節の野菜（トマト・ナス・ほうれん草など）の収穫と、出荷の準備をお願いします。\n・収穫（重い物は男性スタッフが担当します）\n・選別、袋詰め、箱詰め\n・直売所への品出し",
                'requirements' => "未経験の方も歓迎します。\n・屋外での軽作業ができる方",
                'benefits' => "無料駐車場あり／作業着・手袋貸与／野菜のおすそ分けあり",
                'trial_period' => 'なし', 'insurance' => '労災保険', 'smoking' => '屋外に喫煙場所あり（屋内禁煙）',
                'change_scope' => '就業場所：当園の農場・作業場（変更なし）／業務：農作業全般（変更なし）',
                'contract_period' => '期間の定めなし', 'apply_tel' => '028-000-0003',
                'tags' => ['シニア活躍中', '週2〜3日OK', '短時間OK', '未経験OK', '車通勤OK'],
            ],
        ],
    ];
    foreach ($samples as $s) {
        $c = $s['company'];
        Db::exec('INSERT INTO companies (name, contact_name, tel, email, address, created_at, updated_at) VALUES (?,?,?,?,?,?,?)', [$c['name'], $c['contact_name'], $c['tel'], null, $c['address'], $now, $now]);
        $companyId = Db::lastId();
        $j = $s['job'];
        Db::exec(
            'INSERT INTO jobs (company_id, category_id, title, job_name, employment_type, salary_type, salary_min, salary_max, salary_note,
              prefecture, city, address, station, work_days, work_hours, holidays, catch_copy, description, requirements, benefits,
              trial_period, insurance, smoking, change_scope, contract_period, apply_tel, status, valid_until, published_at, created_at, updated_at)
             VALUES (?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?,?)',
            [$companyId, $cat($j['category']), $j['title'], $j['job_name'], $j['employment_type'], $j['salary_type'], $j['salary_min'], $j['salary_max'], $j['salary_note'],
             $j['prefecture'], $j['city'], $j['address'], $j['station'], $j['work_days'], $j['work_hours'], $j['holidays'], $j['catch_copy'], $j['description'], $j['requirements'], $j['benefits'],
             $j['trial_period'], $j['insurance'], $j['smoking'], $j['change_scope'], $j['contract_period'], $j['apply_tel'], 'published', $until, $now, $now, $now]
        );
        $jobId = Db::lastId();
        foreach ($j['tags'] as $t) {
            Db::exec('INSERT INTO job_tags (job_id, tag_id) VALUES (?,?)', [$jobId, $tag($t)]);
        }
    }
    echo "sample jobs ok\n";
}
echo "done\n";
