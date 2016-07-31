<?php

Route::auth();
/*The routes for different page links*/
Route::get('/', 'PageController@index');
Route::get('about', 'PageController@about');
Route::get('contact', 'PageController@contact');

/*The routes for admin*/
Route::get('admin', 'AdminController@index');
Route::get('admin/search_citizens', 'AdminController@searchCitizens');
Route::get('admin/senior_citizens', 'AdminController@seniorCitizens');
Route::get('admin/profile_viewers', 'AdminController@profileViewers');
Route::get('admin/search_viewers', 'AdminController@searchViewers');
Route::get('admin/departments', 'AdminController@departments');
Route::get('admin/edit/{user}', 'AdminController@edit');
Route::get('admin/view/{user}', 'AdminController@show');
Route::get('admin/cvdownload/{detail}', 'AdminController@download');
Route::any('admin/update/{user}', 'AdminController@update');
Route::get('admin/settings', 'AdminController@settings');

/*The routes for verification purposes*/
Route::get('verification/', 'VerificationController@verification' );
Route::get('confirmation/{id}/{code}', 'VerificationController@confirmation');

/*The routes for other users*/
Route::get('profile', 'UserController@index');
Route::get('profile/view', 'UserController@profile');
Route::get('profile/verify_email', 'UserController@startVerification');
Route::get('profile/work_experience', 'UserController@workExperience');
Route::get('profile/edit', 'UserController@edit');
Route::get('search_senior_citizens', 'UserController@searchCitizens');
Route::get('view_senior_citizens', 'UserController@view');
Route::get('view_senior_citizen/{user}', 'UserController@show');
Route::get('cvdownload/{detail}', 'UserController@download');
Route::get('upload', 'UserController@uploadView');
Route::post('profile/add_experience', 'UserController@storeExperience');
Route::any('profile/new', 'UserController@store');
Route::any('profile/update', 'UserController@update');
Route::any('profile/bulk', 'UserController@bulkUpload');

/*The routes for finding autocomplete fields*/
Route::get('search/ministry', 'SearchController@getMinistry');
Route::get('search/department', 'SearchController@getDepartment');
Route::get('search/company', 'SearchController@getCompany');
Route::get('search/location', 'SearchController@getLocation');
Route::get('search/role', 'SearchController@getRole');
Route::get('search/position', 'SearchController@getPosition');
Route::get('search/firstnameviewer', 'SearchController@getFirstnameViewer');

/*The route for errors*/
Route::get('accessError', function() {
	return view('errors.404');
});

/*The routes for mobile site*/
Route::get('mobile/register', 'MobileAuthController@register');
Route::get('mobile/login', 'MobileAuthController@login');

Route::get('mobile/admin', 'MobileAdminController@index');
Route::get('mobile/admin/search_citizens', 'MobileAdminController@searchCitizens');
Route::get('mobile/admin/search_viewers', 'MobileAdminController@searchViewers');
Route::get('mobile/admin/departments', 'MobileAdminController@departments');
Route::get('mobile/admin/edit', 'MobileAdminController@edit');
Route::get('mobile/admin/view', 'MobileAdminController@show');
Route::get('mobile/admin/cvdownload/{detail}', 'MobileAdminController@download');
Route::get('mobile/admin/update', 'MobileAdminController@update');
Route::get('mobile/admin/settings', 'MobileAdminController@settings');

Route::get('mobile/verification/', 'MobileVerificationController@verification' );
Route::get('mobile/confirmation/{id}/{code}', 'MobileVerificationController@confirmation');

Route::get('mobile/profile', 'MobileUserController@index');
Route::get('mobile/profile/view', 'MobileUserController@profile');
Route::get('mobile/profile/verify_email', 'MobileUserController@startVerification');
Route::get('mobile/profile/work_experience', 'MobileUserController@workExperience');
Route::get('mobile/view/senior_citizens', 'MobileUserController@view');
Route::get('mobile/view/senior_citizen', 'MobileUserController@show');
Route::get('mobile/cvdownload/{detail}', 'MobileUserController@download');
Route::get('mobile/profile/add_experience', 'MobileUserController@storeExperience');
Route::get('mobile/profile/new', 'MobileUserController@store');
Route::get('mobile/profile/update', 'MobileUserController@update');
Route::any('mobile/profile/bulk', 'MobileUserController@bulkUpload');
Route::any('mobile/profile/upload/photo', 'MobileUserController@uploadPhoto');

Route::get('mobile/search/category/private', 'MobileSearchController@getPrivateCategory');
Route::get('mobile/search/ministry', 'MobileSearchController@getMinistry');
Route::get('mobile/search/department', 'MobileSearchController@getDepartment');
Route::get('mobile/search/senior_citizen/company', 'MobileSearchController@getCompany');
Route::get('mobile/search/location', 'MobileSearchController@getLocation');
Route::get('mobile/search/role', 'MobileSearchController@getRole');
Route::get('mobile/search/position', 'MobileSearchController@getPosition');
Route::get('mobile/search/companies', 'MobileSearchController@getFirstnameViewer');

/*route for testing*/
Route::get('test/post', function() {
	return view('test.post');
});