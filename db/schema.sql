-- お仕事55号（joboption）スキーマ（MySQL 8 正本）。SQLite 版は db/schema.sqlite.sql（同じ列構成を保つ）
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login_id VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='運営管理者';

CREATE TABLE IF NOT EXISTS login_attempts (
  login_id VARCHAR(50) PRIMARY KEY,
  attempts INT NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ログイン失敗回数';

CREATE TABLE IF NOT EXISTS companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL COMMENT '会社名・店舗名',
  contact_name VARCHAR(100) NULL COMMENT '採用担当者',
  tel VARCHAR(30) NULL,
  email VARCHAR(200) NULL COMMENT '応募通知の送り先',
  zip VARCHAR(10) NULL,
  address VARCHAR(255) NULL,
  url VARCHAR(255) NULL,
  note TEXT NULL COMMENT '運営メモ',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='掲載企業';

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  sort_no INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='職種';

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  sort_no INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='特徴タグ（週2〜OK 等）';

CREATE TABLE IF NOT EXISTS jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  category_id INT NULL,
  title VARCHAR(200) NOT NULL COMMENT '求人タイトル',
  job_name VARCHAR(100) NOT NULL COMMENT '職種名',
  employment_type VARCHAR(20) NOT NULL,
  salary_type VARCHAR(10) NOT NULL COMMENT '時給/日給/月給/年俸',
  salary_min INT NULL,
  salary_max INT NULL,
  salary_note VARCHAR(255) NULL,
  prefecture VARCHAR(10) NOT NULL,
  city VARCHAR(100) NULL,
  address VARCHAR(255) NULL,
  station VARCHAR(100) NULL COMMENT '最寄駅・アクセス',
  work_days VARCHAR(255) NULL COMMENT '勤務日数',
  work_hours VARCHAR(255) NULL COMMENT '勤務時間',
  holidays VARCHAR(255) NULL,
  catch_copy VARCHAR(255) NULL COMMENT '一覧に出すひとこと',
  description TEXT NULL COMMENT '仕事内容',
  requirements TEXT NULL COMMENT '応募資格',
  benefits TEXT NULL COMMENT '待遇・福利厚生',
  trial_period VARCHAR(255) NULL COMMENT '試用期間',
  insurance VARCHAR(255) NULL COMMENT '加入保険',
  smoking VARCHAR(255) NULL COMMENT '受動喫煙対策',
  change_scope VARCHAR(255) NULL COMMENT '就業場所・業務の変更の範囲',
  contract_period VARCHAR(255) NULL COMMENT '契約期間・更新の有無',
  apply_tel VARCHAR(30) NULL COMMENT '電話応募先',
  status VARCHAR(10) NOT NULL DEFAULT 'draft' COMMENT 'draft/published/closed',
  valid_until DATE NULL COMMENT '掲載期限',
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_jobs_status (status, published_at),
  INDEX idx_jobs_pref (prefecture),
  FOREIGN KEY (company_id) REFERENCES companies(id),
  FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='求人';

CREATE TABLE IF NOT EXISTS job_tags (
  job_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (job_id, tag_id),
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='求人と特徴タグ';

CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  tel VARCHAR(30) NULL,
  email VARCHAR(200) NULL,
  message TEXT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'new' COMMENT 'new/contacted/closed',
  memo TEXT NULL COMMENT '運営メモ',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_app_job (job_id),
  FOREIGN KEY (job_id) REFERENCES jobs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='応募';

CREATE TABLE IF NOT EXISTS listing_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(200) NOT NULL,
  contact_name VARCHAR(100) NOT NULL,
  tel VARCHAR(30) NULL,
  email VARCHAR(200) NULL,
  message TEXT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'new' COMMENT 'new/done',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='掲載申込（企業からの問い合わせ）';
