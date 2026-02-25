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
$routes->setAutoRoute(true);

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

// Protected routes (require auth filter)
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('dashboard/(:any)', 'Dashboard::$1', ['filter' => 'auth']);

// Product CRUD routes (protected)
$routes->get('products', 'Product::index', ['filter' => 'auth']);
$routes->get('products/create', 'Product::create', ['filter' => 'auth']);
$routes->post('products/store', 'Product::store', ['filter' => 'auth']);
$routes->get('products/(:num)/image', 'Product::image/$1', ['filter' => 'auth']);
$routes->get('products/(:num)/edit', 'Product::edit/$1', ['filter' => 'auth']);
$routes->post('products/(:num)/update', 'Product::update/$1', ['filter' => 'auth']);
$routes->get('products/(:num)/delete', 'Product::delete/$1', ['filter' => 'auth']);

// Category routes (protected)
$routes->post('categories/store', 'Category::store', ['filter' => 'auth']);
$routes->get('products/(:num)/categories', 'Category::getProductCategories/$1', ['filter' => 'auth']);
$routes->post('products/(:num)/categories/update', 'Category::updateProductCategories/$1', ['filter' => 'auth']);

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
