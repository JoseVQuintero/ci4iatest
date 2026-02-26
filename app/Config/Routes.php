<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Login is the default landing page
$routes->get('/', 'Auth::login');

// authentication
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::store');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/google', 'Auth::socialRedirect/google');
$routes->get('auth/google/callback', 'Auth::socialCallback/google');
$routes->get('auth/github', 'Auth::socialRedirect/github');
$routes->get('auth/github/callback', 'Auth::socialCallback/github');
$routes->get('lang/(:segment)', 'Language::switch/$1');

// Protected routes (require auth filter)
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'module:dashboard']);
$routes->get('dashboard/(:any)', 'Dashboard::$1', ['filter' => 'module:dashboard']);

// Product CRUD routes (protected)
$routes->get('products', 'Product::index', ['filter' => 'module:products']);
$routes->get('products/create', 'Product::create', ['filter' => 'module:products']);
$routes->post('products/store', 'Product::store', ['filter' => 'module:products']);
$routes->get('products/(:num)/image', 'Product::image/$1', ['filter' => 'module:products']);
$routes->get('products/(:num)/edit', 'Product::edit/$1', ['filter' => 'module:products']);
$routes->post('products/(:num)/update', 'Product::update/$1', ['filter' => 'module:products']);
$routes->get('products/(:num)/delete', 'Product::delete/$1', ['filter' => 'module:products']);

// Category routes (protected)
$routes->post('categories/store', 'Category::store', ['filter' => 'module:products']);
$routes->get('products/(:num)/categories', 'Category::getProductCategories/$1', ['filter' => 'module:products']);
$routes->post('products/(:num)/categories/update', 'Category::updateProductCategories/$1', ['filter' => 'module:products']);

// User CRUD routes (admin only)
$routes->get('users', 'User::index', ['filter' => 'module:users']);
$routes->get('users/create', 'User::create', ['filter' => 'module:users']);
$routes->post('users/store', 'User::store', ['filter' => 'module:users']);
$routes->get('users/(:num)/edit', 'User::edit/$1', ['filter' => 'module:users']);
$routes->post('users/(:num)/update', 'User::update/$1', ['filter' => 'module:users']);
$routes->get('users/(:num)/delete', 'User::delete/$1', ['filter' => 'module:users']);

// Role CRUD routes (admin role module)
$routes->get('roles', 'Role::index', ['filter' => 'module:roles']);
$routes->get('roles/create', 'Role::create', ['filter' => 'module:roles']);
$routes->post('roles/store', 'Role::store', ['filter' => 'module:roles']);
$routes->get('roles/(:num)/edit', 'Role::edit/$1', ['filter' => 'module:roles']);
$routes->post('roles/(:num)/update', 'Role::update/$1', ['filter' => 'module:roles']);
$routes->get('roles/(:num)/delete', 'Role::delete/$1', ['filter' => 'module:roles']);

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
