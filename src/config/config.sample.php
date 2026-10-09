<?php
// サーバ用は config.php、ローカルは config.local.php にコピーして値を設定する（Git管理外）
return [
    // 準備サイト・ローカルは SQLite、本番は MySQL を想定（どちらでも動く）
    'db' => ['driver' => 'sqlite', 'path' => __DIR__ . '/../storage/db/joboption.sqlite'],
    // 'db' => ['driver' => 'mysql', 'host' => 'localhost', 'name' => 'DB名', 'user' => 'ユーザー', 'pass' => 'パスワード'],
    'base_path' => '/joboption',   // 公開URLのサブディレクトリ（ルート直下なら ''）
    'debug' => false,
    'noindex' => true,             // 準備サイトでは検索エンジンに載せない
    'site' => [
        'name' => 'joboption',
        'tagline' => '経験をいかして、もう一度はたらく。',
        'url' => 'https://jyunbi.sakura.ne.jp/joboption',
        'operator' => '（運営者名）',
        'operator_address' => '（所在地）',
        'operator_tel' => '（電話番号）',
        'operator_email' => '（メールアドレス）',
    ],
    'mail' => [
        'enabled' => false,         // false の間は storage/logs/mail.log に記録するだけ
        'from' => 'noreply@example.com',
        'admin_to' => '',           // 応募・掲載申込の通知先（運営）
    ],
];
