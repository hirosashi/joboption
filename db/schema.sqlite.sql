-- joboption スキーマ（SQLite 版。正本は db/schema.sql、列構成を揃える）
CREATE TABLE IF NOT EXISTS admins (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  login_id VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS login_attempts (
  login_id VARCHAR(50) PRIMARY KEY,
  attempts INT NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS companies (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(200) NOT NULL,
  contact_name VARCHAR(100) NULL,
  tel VARCHAR(30) NULL,
  email VARCHAR(200) NULL,
  zip VARCHAR(10) NULL,
  address VARCHAR(255) NULL,
  url VARCHAR(255) NULL,
  note TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(100) NOT NULL,
  sort_no INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS tags (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(100) NOT NULL,
  sort_no INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS jobs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  company_id INT NOT NULL,
  category_id INT NULL,
  title VARCHAR(200) NOT NULL,
  job_name VARCHAR(100) NOT NULL,
  employment_type VARCHAR(20) NOT NULL,
  salary_type VARCHAR(10) NOT NULL,
  salary_min INT NULL,
  salary_max INT NULL,
  salary_note VARCHAR(255) NULL,
  prefecture VARCHAR(10) NOT NULL,
  city VARCHAR(100) NULL,
  address VARCHAR(255) NULL,
  station VARCHAR(100) NULL,
  work_days VARCHAR(255) NULL,
  work_hours VARCHAR(255) NULL,
  holidays VARCHAR(255) NULL,
  catch_copy VARCHAR(255) NULL,
  description TEXT NULL,
  requirements TEXT NULL,
  benefits TEXT NULL,
  trial_period VARCHAR(255) NULL,
  insurance VARCHAR(255) NULL,
  smoking VARCHAR(255) NULL,
  change_scope VARCHAR(255) NULL,
  contract_period VARCHAR(255) NULL,
  apply_tel VARCHAR(30) NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'draft',
  valid_until DATE NULL,
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  FOREIGN KEY (company_id) REFERENCES companies(id),
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS job_tags (
  job_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (job_id, tag_id),
  FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS applications (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  job_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  tel VARCHAR(30) NULL,
  email VARCHAR(200) NULL,
  message TEXT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'new',
  memo TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  FOREIGN KEY (job_id) REFERENCES jobs(id)
);

CREATE TABLE IF NOT EXISTS listing_requests (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  company_name VARCHAR(200) NOT NULL,
  contact_name VARCHAR(100) NOT NULL,
  tel VARCHAR(30) NULL,
  email VARCHAR(200) NULL,
  message TEXT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_jobs_status ON jobs (status, published_at);
CREATE INDEX IF NOT EXISTS idx_jobs_pref ON jobs (prefecture);
CREATE INDEX IF NOT EXISTS idx_app_job ON applications (job_id);
