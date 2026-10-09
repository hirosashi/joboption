<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Base;
use App\Core\App;
use App\Core\Auth;
use App\Core\Clock;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Services\Jobs;
use App\Services\Master;

final class JobAdminController extends Base
{
    public const TEXT_FIELDS = [
        'title', 'job_name', 'employment_type', 'salary_type', 'salary_note', 'prefecture', 'city', 'address', 'station',
        'work_days', 'work_hours', 'holidays', 'catch_copy', 'description', 'requirements', 'benefits',
        'trial_period', 'insurance', 'smoking', 'change_scope', 'contract_period', 'apply_tel', 'status',
    ];

    public static function index(): void
    {
        Auth::requireLogin();
        $status = App::param('status');
        $where = '1=1';
        $params = [];
        if (isset(Master::JOB_STATUS[$status])) {
            $where = 'j.status = ?';
            $params[] = $status;
        }
        $rows = Db::rows("SELECT j.*, c.name AS company_name, (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS app_count
            FROM jobs j JOIN companies c ON c.id = j.company_id WHERE $where ORDER BY j.id DESC", $params);
        View::render('admin/jobs/index', ['title' => '求人', 'rows' => $rows, 'status' => $status], 'admin/layout');
    }

    public static function edit(): void
    {
        Auth::requireLogin();
        $id = App::intParam('id');
        $job = $id > 0 ? Jobs::find($id) : null;
        if ($id > 0 && $job === null) {
            self::notFound();
        }
        if ($job === null) {
            $job = array_fill_keys(self::TEXT_FIELDS, '') + ['id' => 0, 'company_id' => App::intParam('company_id'), 'category_id' => 0, 'salary_min' => null, 'salary_max' => null, 'valid_until' => Clock::now()->modify('+90 days')->format('Y-m-d'), 'tags' => []];
            $job['employment_type'] = 'part';
            $job['salary_type'] = 'hour';
            $job['status'] = 'draft';
        }
        $companies = [];
        foreach (Db::rows('SELECT id, name FROM companies ORDER BY name') as $c) {
            $companies[(int)$c['id']] = (string)$c['name'];
        }
        View::render('admin/jobs/edit', [
            'title' => $job['id'] ? '求人を直す' : '求人を登録',
            'job' => $job,
            'companies' => $companies,
            'categories' => Master::categories(),
            'tags' => Master::tags(),
        ], 'admin/layout');
    }

    public static function save(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $id = App::intParam('id');
        $v = new Validator($_POST);
        $v->required('title', '求人タイトル')->maxLength('title', 200, '求人タイトル')
          ->required('job_name', '職種名')->maxLength('job_name', 100, '職種名')
          ->required('prefecture', '都道府県')->number('salary_min', '給与（下限）')->number('salary_max', '給与（上限）')->date('valid_until', '掲載期限');
        $companyId = App::intParam('company_id');
        $errors = $v->errors();
        if (Db::value('SELECT id FROM companies WHERE id = ?', [$companyId]) === null) {
            $errors[] = '掲載企業を選んでください。';
        }
        $d = [];
        foreach (self::TEXT_FIELDS as $k) {
            $d[$k] = Validator::toStr($_POST[$k] ?? '');
        }
        if (!isset(Master::EMPLOYMENT_TYPES[(string)$d['employment_type']]) || !isset(Master::SALARY_TYPES[(string)$d['salary_type']]) || !isset(Master::JOB_STATUS[(string)$d['status']]) || !in_array($d['prefecture'], Master::PREFECTURES, true)) {
            $errors[] = '選択項目の値が正しくありません。';
        }
        if ($errors !== []) {
            Session::flash('error', implode(' ', $errors));
            App::redirect('/admin/jobs/edit?id=' . $id . ($id ? '' : '&company_id=' . $companyId));
        }
        $toInt = static fn(string $k): ?int => ($n = Validator::toNum($_POST[$k] ?? '')) === null ? null : (int)$n;
        $categoryId = App::intParam('category_id');
        $d['company_id'] = $companyId;
        $d['category_id'] = isset(Master::categories()[$categoryId]) ? $categoryId : null;
        $d['salary_min'] = $toInt('salary_min');
        $d['salary_max'] = $toInt('salary_max');
        $d['valid_until'] = Validator::toDate($_POST['valid_until'] ?? '');
        $now = Clock::nowStr();
        $tagIds = array_values(array_intersect(array_map('intval', (array)($_POST['tags'] ?? [])), array_keys(Master::tags())));

        $id = Db::tx(static function () use ($id, $d, $now, $tagIds): int {
            $old = $id > 0 ? Db::row('SELECT status, published_at FROM jobs WHERE id = ?', [$id]) : null;
            $d['published_at'] = $old['published_at'] ?? null;
            if ($d['status'] === 'published' && $d['published_at'] === null) {
                $d['published_at'] = $now;
            }
            $d['updated_at'] = $now;
            $cols = array_keys($d);
            if ($old === null) {
                $d['created_at'] = $now;
                $cols = array_keys($d);
                Db::exec('INSERT INTO jobs (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')', array_values($d));
                $id = Db::lastId();
            } else {
                Db::exec('UPDATE jobs SET ' . implode(',', array_map(static fn(string $c): string => "$c = ?", $cols)) . ' WHERE id = ?', [...array_values($d), $id]);
            }
            Db::exec('DELETE FROM job_tags WHERE job_id = ?', [$id]);
            foreach ($tagIds as $t) {
                Db::exec('INSERT INTO job_tags (job_id, tag_id) VALUES (?,?)', [$id, $t]);
            }
            return $id;
        });
        Session::flash('ok', '保存しました。');
        App::redirect('/admin/jobs/edit?id=' . $id);
    }

    public static function copy(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $job = Jobs::find(App::intParam('id'));
        if ($job === null) {
            self::notFound();
        }
        $now = Clock::nowStr();
        $newId = Db::tx(static function () use ($job, $now): int {
            $d = [];
            foreach (array_merge(self::TEXT_FIELDS, ['company_id', 'category_id', 'salary_min', 'salary_max', 'valid_until']) as $k) {
                $d[$k] = $job[$k];
            }
            $d['title'] = $job['title'] . '（コピー）';
            $d['status'] = 'draft';
            $d['created_at'] = $now;
            $d['updated_at'] = $now;
            $cols = array_keys($d);
            Db::exec('INSERT INTO jobs (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')', array_values($d));
            $newId = Db::lastId();
            foreach (array_keys($job['tags']) as $t) {
                Db::exec('INSERT INTO job_tags (job_id, tag_id) VALUES (?,?)', [$newId, $t]);
            }
            return $newId;
        });
        Session::flash('ok', 'コピーしました（下書き）。');
        App::redirect('/admin/jobs/edit?id=' . $newId);
    }

    public static function delete(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $id = App::intParam('id');
        if ((int)Db::value('SELECT COUNT(*) FROM applications WHERE job_id = ?', [$id]) > 0) {
            Db::exec("UPDATE jobs SET status = 'closed', updated_at = ? WHERE id = ?", [Clock::nowStr(), $id]);
            Session::flash('warn', '応募がある求人なので削除せず「掲載終了」にしました。');
        } else {
            Db::exec('DELETE FROM jobs WHERE id = ?', [$id]);
            Session::flash('ok', '削除しました。');
        }
        App::redirect('/admin/jobs');
    }
}
