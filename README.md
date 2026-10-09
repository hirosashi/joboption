# joboption

年齢を問わず「自分のペースで働きたい」方向けのシンプルな求人サイト（55歳以上が働きやすい求人が中心。年齢制限はしない）。
モットーは **シンプル・登録が簡単・見やすい**。

- 準備サイト: https://jyunbi.sakura.ne.jp/joboption/ （管理画面は `/joboption/admin`）
- 構成: PHP 8.1 + PDO（SQLite / MySQL どちらでも動く）。フレームワーク・Composer なし。

## 機能（Phase 1：運営が求人を代行登録）

| 公開側 | 管理画面 |
|---|---|
| トップ（検索・こだわりタグ・新着） | ホーム（未対応件数・最近の応募） |
| 求人一覧（エリア・職種・こだわり・キーワード） | 求人の登録／編集／コピー／削除・特徴タグ |
| 求人詳細（募集要項・JobPosting構造化データ・固定の応募ボタン） | 企業の登録／編集 |
| 応募（会員登録なし：名前・連絡先・ひとこと → 確認 → 完了） | 応募一覧・詳細・対応状況とメモ・CSV |
| 掲載申込（企業向け） | 掲載申込一覧 |
| 運営者情報・利用規約・プライバシーポリシー・sitemap.xml・robots.txt | 職種・タグのマスタ、パスワード変更 |

## ディレクトリ

```
src/                 ← 公開ディレクトリ（サーバの www/joboption に置く）
  index.php          ← フロントコントローラ（ルート一覧）
  app/Core           ← App, Router, Db, View, Csrf, Session, Auth, Validator, Clock
  app/Controllers    ← 公開側・Admin
  app/Services       ← Jobs（検索・表示・構造化データ）, Master, Mailer
  app/Views          ← テンプレート
  assets/style.css
  config/config.sample.php ← config.php（サーバ）/ config.local.php（ローカル）にコピー。Git管理外
  storage/db, storage/logs ← SQLite と mail.log（Web非公開）
db/schema.sql, db/schema.sqlite.sql
tools/install.php    ← テーブル作成＋管理者＋マスタ＋サンプル求人3件（既にあれば何もしない）
dev_router.php       ← php -S 用
```

## ローカルで動かす

```bash
cp src/config/config.sample.php src/config/config.local.php   # base_path を '' に、debug を true に
php tools/install.php src '<管理者パスワード8文字以上>'          # 管理者IDは admin
php -S 127.0.0.1:8090 -t src dev_router.php
# http://127.0.0.1:8090/ ・ http://127.0.0.1:8090/admin
```

## 準備サイトへの反映

```bash
./deploy.sh            # SAKURA_SSH_HOST / SAKURA_SSH_USER / 鍵 ~/.ssh/sakura_jo を使用
```

- サーバ側の `config/config.php` と `storage/`（DB・ログ）は上書きしない。
- 反映前に `~/backups/joboption_YYYYmmdd_HHMMSS.tar.gz` を作る。
- 初回のみサーバで `php tools/install.php ~/www/joboption '<パスワード>'` を実行（tools はサーバの `~/joboption_tools` に置く）。

## 規約

- SQL はすべてプリペアドステートメント、出力は `View::e()`、POST は `Csrf::verify()`。
- 業務日時は `Clock`（Asia/Tokyo）を使い、`date()` / `time()` / SQL の `NOW()` は使わない。MySQL は `DATETIME`。
- メール：`mail.enabled=false` の間は `storage/logs/mail.log` に書くだけ。

## 公開前に決めること

- 運営者情報（config の site.operator など）、利用規約・プライバシーポリシーの確定
- 特定募集情報等提供事業の届出（受理番号を運営者情報に記載）
- 通知メールの送信元・運営の通知先（config の mail）
- 本番で `noindex` を false にして Google しごと検索に載せる
