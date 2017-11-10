<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
#Dusterio\LumenPassport\LumenPassport::routes($app);
$app->get('/', function () use ($app) {
	echo phpinfo();
    return $app->version();
});
#$app->get('/test', 'UserController@test');
$app->get('/home', 'HomeController@show');
$app->get('/home1', 'HomeController_new@show');
$app->post('/merchants', 'MerchantsController@create');
$app->get('/merchants', 'MerchantsController@show');
$app->get('/merchants/{id:[0-9]+}', 'MerchantsController@get_merchant_details');
$app->post('/merchants/validate', 'MerchantsController@verify_merchant');
$app->put('/merchants/{id:[0-9]+}', 'MerchantsController@update_merchant_details');
$app->put('/merchants/changepassword', 'MerchantsController@update_password');

$app->get('/loginuser', 'UserController@show');
$app->get('/loginuser/{id:[0-9]+}', 'UserController@getuser');
$app->get('/checkuser/{email}', 'UserController@getuser_by_email');
$app->get('/testmongo', 'UserController@check_mongo');
$app->post('/loginuser', 'UserController@create');
$app->post('/loginuser/validate', 'UserController@verify_user');

$app->get('/adminuser', 'AdminController@show');
$app->get('/adminuser/{id:[0-9]+}', 'AdminController@getadminuser');
$app->get('/adminuser/{email}', 'AdminController@getadmin_by_email');
$app->post('/adminuser', 'AdminController@create');
$app->post('/adminuser/validate', 'AdminController@verify_adminuser');
$app->put('/adminuser/{id:[0-9]+}', 'AdminController@update_adminuser');

$app->post('/banner', 'BannerController@create');
$app->get('/banner', 'BannerController@show');
$app->get('/banner/{id:[0-9]+}', 'BannerController@getbanner');
$app->put('/banner/{id:[0-9]+}', 'BannerController@update_banner');

$app->post('/countries', 'CountryController@create');
$app->get('/countries', 'CountryController@show');
$app->get('/countries/{id:[0-9]+}', 'CountryController@getcountry');
$app->put('/countries/{id:[0-9]+}', 'CountryController@update_country');

$app->post('/cities', 'CityController@create');
$app->get('/cities', 'CityController@show');
$app->get('/cities/popular', 'CityController@get_popular_city');
$app->get('/cities/{id:[0-9]+}', 'CityController@getcity');
$app->put('/cities/{id:[0-9]+}', 'CityController@update_city');

$app->post('/currencies', 'CurrencyController@create');
$app->get('/currencies', 'CurrencyController@show');
$app->get('/currencies/{id:[0-9]+}', 'CurrencyController@getcurrency');
$app->put('/currencies/{id:[0-9]+}', 'CurrencyController@update_currency');

$app->post('/pages', 'PagesController@create');
$app->get('/pages', 'PagesController@show');
$app->get('/pages/{id:[0-9]+}', 'PagesController@get_page_details');
$app->put('/pages/{id:[0-9]+}', 'PagesController@update_page_details');
$app->get('/pages/{page_title}', 'PagesController@getpage_by_title');

$app->post('/categories', 'CategoriesController@create');
$app->get('/categories', 'CategoriesController@show');
$app->get('/categories/{id:[0-9]+}', 'CategoriesController@get_category_details');
$app->put('/categories/{id:[0-9]+}', 'CategoriesController@update_category_details');
$app->get('localization/home/{lang}','LanguageLocalizationController@home');

$app->post('activities','ActivityController@create');
$app->get('/activities','ActivityController@show');
$app->get('/activities/{id:[0-9]+}', 'ActivityController@get_activity_details');
$app->get('/activitypackages/{id:[0-9]+}', 'ActivityController@get_activity_pacakge_details');
$app->put('/activities/{id:[0-9]+}', 'ActivityController@update_activity_details');
$app->get('/merchantactivities/{id1:[0-9]+}/{id2:[0-9]+}', 'ActivityController@get_merchant_activity_details');
$app->get('/merchantactivities/{id:[0-9]+}','ActivityController@show_merchant_activities');
$app->get('/cityactivities/{id:[0-9]+}','ActivityController@show_city_activities');

$app->post('/packages','PackagesController@create');
$app->get('/packages','PackagesController@show');
$app->get('/packages/{id:[0-9]+}', 'PackagesController@get_package_details');
$app->put('/packages/{id:[0-9]+}', 'PackagesController@update_package_details');
$app->get('/merchantpackages/{id1:[0-9]+}/{id2:[0-9]+}', 'PackagesController@get_merchant_packages_details');
$app->get('/merchantpackages/{id:[0-9]+}','PackagesController@show_merchant_packages');

$app->get('/shopper', 'ShopperController@show');
$app->get('/shopper/{id:[0-9]+}', 'ShopperController@getshopper');
$app->get('/checkshopper/{email}', 'ShopperController@getshopper_by_email');
$app->post('/shopper','ShopperController@create');
$app->put('/shopper/{id:[0-9]+}', 'ShopperController@update_shopper');
$app->post('/shopper/validate', 'ShopperController@verify_shopper');
//$app->post('/addtocart','ShopperController@add_to_cart');
$app->post('/addtocart','ShopperController@add_to_cart_new');
$app->get('/addtocart/{id:[0-9]+}','ShopperController@get_add_to_cart_details');

$app->put('/addtocart/{id1:[0-9]+}/{id2:[0-9]+}', 'ShopperController@update_add_to_cart_details');
$app->delete('/addtocart/{id1:[0-9]+}/{id2:[0-9]+}', 'ShopperController@delete_add_to_cart_details');



$app->post('/orders','OrdersController@create');
$app->post('/Checkout','OrdersController@Order_Checkout');

$app->post('/resetpassword','MailerController@send_reset_password_mail');
$app->get('/pgateways','PaymentController@show');
$app->post('/charge','PaymentController@payfort_charge');
$app->get('/paypalcharge','PaymentController@paypal_charge');

//ShopperController
#$app->get('/oauth/tokens','\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenControlle');
$app->post('/voucher', 'VoucherController@create');
$app->get('/voucher', 'VoucherController@get_voucher_details');
$app->get('/mvoucher/{id1:[0-9]+}', 'VoucherController@get_merchant_voucher_details');
$app->get('/checkvoucher/{id1:[0-9]+}/{voucher_code}', 'VoucherController@check_voucher_code');
$app->get('/merchantvoucher/{id:[0-9]+}', 'VoucherController@get_merhcant_voucher_details');
$app->put('/voucher/{id:[0-9]+}', 'VoucherController@update_voucher_details');
