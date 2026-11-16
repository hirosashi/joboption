<?php
// サーバ用は config.php、ローカルは config.local.php にコピーして値を設定する（Git管理外）
return [
    // SQLite・MySQL のどちらでも動く
    'db' => ['driver' => 'sqlite', 'path' => __DIR__ . '/../storage/db/joboption.sqlite'],
    // 'db' => ['driver' => 'mysql', 'host' => 'localhost', 'name' => 'DB名', 'user' => 'ユーザー', 'pass' => 'パスワード'],
    'base_path' => '',            // 公開URLのサブディレクトリ（ルート直下なら ''）
    'debug' => false,
    'noindex' => true,             // 公開サイトでは false にして検索エンジンに載せる
    'ga_measurement_id' => '',     // Googleアナリティクス4 の測定ID（例：G-XXXXXXXXXX）。空なら計測タグを出さない
    'site' => [
        'name' => 'お仕事55号',
        'tagline' => '経験をいかして、もう一度はたらく。',
        'url' => 'https://oshigoto55.com',
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
