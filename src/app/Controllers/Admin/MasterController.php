<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Base;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\Session;
use App\Core\App;
use App\Core\View;

/** 職種・特徴タグの編集（名前と並び順。使われていないものだけ削除できる） */
final class MasterController extends Base
{
    private const TABLES = ['categories' => '職種', 'tags' => '特徴タグ'];

    public static function index(): void
    {
        Auth::requireLogin();
        View::render('admin/masters', [
            'title' => '職種・タグ',
            'lists' => [
                'categories' => Db::rows('SELECT c.*, (SELECT COUNT(*) FROM jobs j WHERE j.category_id = c.id) AS used FROM categories c ORDER BY sort_no, id'),
                'tags' => Db::rows('SELECT t.*, (SELECT COUNT(*) FROM job_tags jt WHERE jt.tag_id = t.id) AS used FROM tags t ORDER BY sort_no, id'),
            ],
            'labels' => self::TABLES,
        ], 'admin/layout');
    }

    public static function save(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $table = App::param('table');
        if (!isset(self::TABLES[$table])) {
            App::redirect('/admin/masters');
        }
        $rows = $_POST['rows'] ?? [];
        Db::tx(static function () use ($table, $rows): void {
            foreach (is_array($rows) ? $rows : [] as $id => $r) {
                $name = trim((string)($r['name'] ?? ''));
                $sort = (int)($r['sort_no'] ?? 0);
                if ((string)$id === 'new') {
                    if ($name !== '') {
                        Db::exec("INSERT INTO $table (name, sort_no) VALUES (?,?)", [mb_substr($name, 0, 100), $sort]);
                    }
                    continue;
                }
                if (!empty($r['delete'])) {
                    $used = $table === 'categories'
                        ? (int)Db::value('SELECT COUNT(*) FROM jobs WHERE category_id = ?', [(int)$id])
                        : (int)Db::value('SELECT COUNT(*) FROM job_tags WHERE tag_id = ?', [(int)$id]);
                    if ($used === 0) {
                        Db::exec("DELETE FROM $table WHERE id = ?", [(int)$id]);
                    }
                    continue;
                }
                if ($name !== '') {
                    Db::exec("UPDATE $table SET name = ?, sort_no = ? WHERE id = ?", [mb_substr($name, 0, 100), $sort, (int)$id]);
                }
            }
        });
        Session::flash('ok', self::TABLES[$table] . 'を保存しました。');
        App::redirect('/admin/masters');
    }
}
