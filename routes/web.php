<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Product;
use Illuminate\Support\Facades\Auth;


Auth::routes(['verify' => true]);
// RESTAURANT ROUTES
Route::get('/', function (){ return view('pages.index'); })->name('home');
Route::get('/home', function (){ return view('pages.index'); })->name('home');

Route::get('menukaart', function () {
	$menu = Product::all();
    return view('pages.menu', compact('menu'));
})->name('menukaart');

Route::post('contact', 'ContactController@store')->name('contact.store');

Route::get('about', function () { return view('pages.about'); })->name('about');
Route::get('contact', function () { return view('pages.contact'); })->name('contact');

Route::group(['middleware' => ['auth', 'cstatus']], function () {
	// Route::get('reserveer', function () { return view('pages.reservation'); })->name('reserveer');
	Route::post('reserveer', 'ReservationController@reservate');
	Route::get('reserveer', 'ReservationController@index')->name('reserveer_index');

	Route::get('profile/edit/{customer_nr}', 'AccountController@get_user')->name('get_user');
	Route::post('profile/edit/{customer_nr}', 'AccountController@edit_user')->name('edit_user');

	Route::get('profile/block/{customer_nr}', 'AccountController@block_user')->name('block_user');
	Route::post('profile/block/{customer_nr}', 'AccountController@block_user')->name('block_user');

	Route::get('profile/delete/{customer_nr}', 'AccountController@delete_account')->name('delete_account');
	Route::post('profile/delete/{customer_nr}', 'AccountController@delete_account')->name('delete_account');

	Route::get('profile/password', 'AccountController@reset_password')->name('reset_password');
	Route::post('profile/password/update/{customer_nr}', 'AccountController@password_update')->name('password_update');

	Route::get('profile/{user}', 'AccountController@show_user')->name('profile_index');

	Route::get('profile/notes/{reservation_nr}/{customer_nr}', 'CreatepdfController@index');
	Route::get('profile/notes/{reservation_nr}/{customer_nr}/download', 'CreatepdfController@downloadPDF');

	Route::get('profile/{user}/bestelling/{reservation_nr}', 'OrderController@view_customer_order_user');

	Route::delete('profile/delete/reservation/{reservation_nr}', 'ReservationController@deleteReservation');

	Route::post('profile/api', 'ReservationController@api');
});

Route::group(['middleware' => 'role:administrator'], function() {
    Route::get('/admin', 'AdminController@welcome')->name('admin_home');
    Route::get('/admin/users', ['middleware' => ['permission:manage-admins'], 'uses' => 'AdminController@manageAdmins']);
	Route::get('/admin/bestellingen', 'OrderController@index');
	Route::get('/admin/export', 'ReservationController@export')->name('export');
	Route::post('/admin/export', 'ReservationController@export')->name('export');
});

Route::group(['middleware' => 'permission:read-order,create-order,delete-order,update-order'], function () {
	Route::get('/bestellingen', 'OrderController@index')->name('orders_home');
	Route::get('/bestelling/{customer_nr}', 'OrderController@view_customer_order')->name('view_customer_order');
	Route::get('/bestellingen/{customer_nr}', 'OrderController@view_customer_order_json');

	Route::post('/bestellingen/new', 'OrderController@new_order');
});