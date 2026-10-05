<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/**
 * ------------------------------------------------------------------
 * LavaLust - an opensource lightweight PHP MVC Framework
 * ------------------------------------------------------------------
 *
 * MIT License
 *
 * Copyright (c) 2020 Ronald M. Marasigan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package LavaLust
 * @author Ronald M. Marasigan <ronald.marasigan@yahoo.com>
 * @since Version 1
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/*
| -------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------
| Here is where you can register web routes for your application.
|
|
*/
/** @var object $router **/

// Load middleware registrations early so kernel Middleware class can see them
// before the router dispatches (Config::load() would run too late for this).
// Wrapped in a closure so the local $config array inside middleware.php
// does not collide with the global $config Config object used elsewhere.
(function () {
    require_once APP_DIR . 'config/middleware.php';
    get_config($config);
})();

$router->get('/', 'Welcome::index');

$router->get('/student', 'StudentController::index');
$router->get('/student/profile', 'StudentController::profile')->middleware('student');

$router->get('/users', 'UsersController::index');

$router->get('/login', 'AuthController::login');
$router->post('/login', 'AuthController::authenticate');
$router->get('/register', 'AuthController::register');
$router->post('/register', 'AuthController::store_register');
$router->get('/logout', 'AuthController::logout');

$router->get('/products', 'ProductController::index')->middleware('auth');
$router->get('/products/create', 'ProductController::create')->middleware(['auth', 'admin']);
$router->post('/products/create', 'ProductController::store')->middleware(['auth', 'admin']);
$router->get('/products/edit/{id}', 'ProductController::edit')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/products/edit/{id}', 'ProductController::update')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/products/delete/{id}', 'ProductController::delete')->middleware(['auth', 'admin'])->where_number('id');


// ------------------------------------------------------------------
// Migration Routes (Laboratory Activity: Database Migration)
// Blocked in the browser when APP_ENV=production (see MigrationController)
// ------------------------------------------------------------------
$router->get('create-migration/{migration_class}', 'MigrationController::create_migration');
$router->get('migrate', 'MigrationController::migrate');
$router->get('rollback', 'MigrationController::rollback');
$router->get('rollback-all', 'MigrationController::rollback_all');
$router->get('refresh', 'MigrationController::refresh');
$router->get('status', 'MigrationController::status');

// ------------------------------------------------------------------
// REST API (Laboratory Exercise No. 6)
// ------------------------------------------------------------------
$router->post('/api/register', 'ApiController::register');
$router->post('/api/login', 'ApiController::login');
$router->post('/api/refresh', 'ApiController::refresh');
$router->post('/api/logout', 'ApiController::logout');
$router->get('/api/me', 'ApiController::me');

$router->get('/api/products', 'ApiController::products_index');
$router->post('/api/products', 'ApiController::products_store');
$router->get('/api/products/{id}', 'ApiController::products_show')->where_number('id');
$router->put('/api/products/{id}', 'ApiController::products_update')->where_number('id');
$router->patch('/api/products/{id}', 'ApiController::products_update')->where_number('id');
$router->delete('/api/products/{id}', 'ApiController::products_delete')->where_number('id');

// CORS preflight (browsers send OPTIONS before PUT/PATCH/DELETE/JSON requests)
foreach (['/api/register', '/api/login', '/api/refresh', '/api/logout', '/api/me', '/api/products'] as $path) {
    $router->options($path, 'ApiController::preflight');
}
$router->options('/api/products/{id}', 'ApiController::preflight')->where_number('id');
