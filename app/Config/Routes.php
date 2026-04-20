<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Startpage::index');
$routes->get('/opensearch.xml', 'Startpage::opensearch');

// Startpage management routes
$routes->get('/start/search', 'Startpage_search::index');
$routes->get('/start/search/datatable', 'Startpage_search::datatable');
$routes->post('/start/search/add', 'Startpage_search::add');
$routes->post('/start/search/edit', 'Startpage_search::edit');
$routes->post('/start/search/delete', 'Startpage_search::delete');
$routes->get('/start/history', 'Startpage_history::index');
$routes->post('/start/history/delete', 'Startpage_history::delete');
$routes->get('/start/redirects', 'Startpage_redirects::index');
$routes->get('/start/redirects/datatable', 'Startpage_redirects::datatable');
$routes->post('/start/redirects/add', 'Startpage_redirects::add');
$routes->post('/start/redirects/edit', 'Startpage_redirects::edit');
$routes->post('/start/redirects/delete', 'Startpage_redirects::delete');

// Admin routes
$routes->get('/admin', 'Admin\Home::index');

// Admin shortcuts routes
$routes->get('/admin/shortcuts', 'Admin\Shortcuts::index');
$routes->post('/admin/shortcuts/category/add', 'Admin\Shortcuts::categoryAdd');
$routes->post('/admin/shortcuts/category/edit', 'Admin\Shortcuts::categoryEdit');
$routes->post('/admin/shortcuts/category/delete', 'Admin\Shortcuts::categoryDelete');
$routes->post('/admin/shortcuts/category/reorder', 'Admin\Shortcuts::categoryReorder');
$routes->post('/admin/shortcuts/add', 'Admin\Shortcuts::shortcutAdd');
$routes->post('/admin/shortcuts/edit', 'Admin\Shortcuts::shortcutEdit');
$routes->post('/admin/shortcuts/delete', 'Admin\Shortcuts::shortcutDelete');
$routes->post('/admin/shortcuts/reorder', 'Admin\Shortcuts::shortcutReorder');

// Admin import / export routes
$routes->get('/admin/import-export', 'Admin\ImportExport::index');
$routes->get('/admin/export/history', 'Admin\ImportExport::exportHistory');
$routes->get('/admin/export/redirects', 'Admin\ImportExport::exportRedirects');
$routes->get('/admin/export/search', 'Admin\ImportExport::exportSearch');
$routes->post('/admin/import/history', 'Admin\ImportExport::importHistory');
$routes->post('/admin/import/redirects', 'Admin\ImportExport::importRedirects');
$routes->post('/admin/import/search', 'Admin\ImportExport::importSearch');

// API routes
$routes->match(['get', 'options'], '/api/test/ping', 'Api\Test::ping');

// Startpage command route
$routes->post('/command', 'Startpage::command');

// Command line routes
$routes->cli('cli/test/index/(:segment)', 'CLI\Test::index/$1');
$routes->cli('cli/test/count', 'CLI\Test::count');

// Metrics route
$routes->post('/metrics/receive', 'Metrics::receive');

// Logout route
$routes->get('/logout', 'Auth::logout');

// Unauthorised route
$routes->get('/unauthorised', 'Unauthorised::index');

// Custom 404 route
$routes->set404Override('App\Controllers\Errors::show404');

// Debug routes
$routes->get('/debug', 'Debug\Home::index');
$routes->get('/debug/(:segment)', 'Debug\Rerouter::reroute/$1');
$routes->get('/debug/(:segment)/(:segment)', 'Debug\Rerouter::reroute/$1/$2');
