<?php
declare(strict_types=1);

use App\Controllers\Admin\AdminAuthController;
use App\Controllers\Admin\AdminHomeController;
use App\Controllers\Admin\ApplicationController;
use App\Controllers\Admin\CompanyController;
use App\Controllers\Admin\JobAdminController;
use App\Controllers\Admin\MasterController;
use App\Controllers\Admin\RequestController;
use App\Controllers\ApplyController;
use App\Controllers\HomeController;
use App\Controllers\JobController;
use App\Controllers\PageController;
use App\Core\App;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/storage/logs/php_error.log');

require __DIR__ . '/app/bootstrap.php';

Session::start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

$router = new Router();

// 求職者向け
$router->get('/', [HomeController::class, 'index']);
$router->get('/jobs', [JobController::class, 'index']);
$router->get('/jobs/{id}', [JobController::class, 'show']);
$router->get('/jobs/{id}/apply', [ApplyController::class, 'form']);
$router->post('/jobs/{id}/apply/confirm', [ApplyController::class, 'confirm']);
$router->post('/jobs/{id}/apply/send', [ApplyController::class, 'send']);
$router->get('/jobs/{id}/apply/done', [ApplyController::class, 'done']);

// 企業向け・固定ページ
$router->get('/for-companies', [PageController::class, 'companies']);
$router->post('/for-companies/send', [PageController::class, 'companiesSend']);
$router->get('/for-companies/done', [PageController::class, 'companiesDone']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/terms', [PageController::class, 'terms']);
$router->get('/privacy', [PageController::class, 'privacy']);
$router->get('/robots.txt', [PageController::class, 'robots']);
$router->get('/sitemap.xml', [PageController::class, 'sitemap']);

// 運営管理
$router->get('/admin/login', [AdminAuthController::class, 'loginForm']);
$router->post('/admin/login', [AdminAuthController::class, 'login']);
$router->post('/admin/logout', [AdminAuthController::class, 'logout']);
$router->get('/admin/password', [AdminAuthController::class, 'passwordForm']);
$router->post('/admin/password', [AdminAuthController::class, 'password']);
$router->get('/admin', [AdminHomeController::class, 'index']);
$router->get('/admin/jobs', [JobAdminController::class, 'index']);
$router->get('/admin/jobs/edit', [JobAdminController::class, 'edit']);
$router->post('/admin/jobs/save', [JobAdminController::class, 'save']);
$router->post('/admin/jobs/copy', [JobAdminController::class, 'copy']);
$router->post('/admin/jobs/delete', [JobAdminController::class, 'delete']);
$router->get('/admin/companies', [CompanyController::class, 'index']);
$router->get('/admin/companies/edit', [CompanyController::class, 'edit']);
$router->post('/admin/companies/save', [CompanyController::class, 'save']);
$router->get('/admin/applications', [ApplicationController::class, 'index']);
$router->get('/admin/applications/view', [ApplicationController::class, 'view']);
$router->post('/admin/applications/save', [ApplicationController::class, 'save']);
$router->get('/admin/applications/csv', [ApplicationController::class, 'csv']);
$router->get('/admin/requests', [RequestController::class, 'index']);
$router->post('/admin/requests/save', [RequestController::class, 'save']);
$router->get('/admin/masters', [MasterController::class, 'index']);
$router->post('/admin/masters/save', [MasterController::class, 'save']);

try {
    $router->dispatch();
} catch (\Throwable $e) {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    View::render('errors/500', ['title' => 'エラー', 'debug' => (bool)(App::config()['debug'] ?? false), 'error' => $e]);
}
