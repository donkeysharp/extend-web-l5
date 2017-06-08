<?php

// Common Routes
Route::get('/', ['uses' => 'HomeController@index']);
Route::get('/login', ['as' => 'login', 'uses' => 'HomeController@login']);
Route::post('/login', ['as' => 'login', 'uses' => 'HomeController@doLogin']);
Route::post('/logout', ['uses' => 'HomeController@doLogout']);

Route::any('/blank', function() {
    return '';
});

Route::get('/public/bulletins/{id}', ['uses' => 'BulletinController@publicDisplay']);

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', ['uses' => 'DashboardController@index']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('/subtitles', ['uses' => 'SubtitleController@index']);
    Route::post('/subtitles', ['uses' => 'SubtitleController@store']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard/sources', ['uses' => 'SourceController@index']);
    Route::get('/dashboard/sources/create', ['uses' => 'SourceController@create']);
    Route::get('/dashboard/sources/{id}/edit', ['uses' => 'SourceController@edit']);
    Route::get('/sources', ['uses' => 'SourceController@index']);
    Route::post('/sources', ['uses' => 'SourceController@store']);
    Route::put('/sources/{id}', ['uses' => 'SourceController@update']);
    Route::delete('/sources/{id}', ['uses' => 'SourceController@destroy']);
});

Route::group(['middleware' => 'auth'], function(){
    Route::get('dashboard/news', ['uses' => 'NewsController@index']);

    Route::get('dashboard/news/create', ['uses' => 'NewsController@create']);
    Route::get('dashboard/news/{id}/edit', ['uses' => 'NewsController@edit']);
    Route::get('dashboard/news/{id}/view', ['uses' => 'NewsController@view'])->where('id', '[0-9]+');
    Route::get('/news/extra', ['uses' => 'NewsController@extra']);
    Route::get('/news/{id}', ['uses' => 'NewsController@show'])->where('id', '[0-9]+');
    Route::get('news/{id}/uploads', ['uses' => 'NewsController@getUploads']);
    Route::get('news/{id}/urls', ['uses' => 'NewsController@getURLS']);

    Route::post('/news', ['uses' => 'NewsController@store']);
    Route::put('/news/{id}', ['uses' => 'NewsController@update']);
    Route::delete('/news/{id}', ['uses' => 'NewsController@destroy']);
    Route::delete('/news/{id}/details/{detailId}', ['uses' => 'NewsController@destroyDetail']);
    Route::post('/news/{id}/uploads', ['uses' => 'NewsController@upload']);
    Route::post('/news/{id}/urls', ['uses' => 'NewsController@addURL']);
    Route::post('/news/{id}/copy/{clientId}', ['uses' => 'NewsController@copyNews']);
    Route::delete('/news/{id}/uploads/{uploadId}', ['uses' => 'NewsController@destroyUpload']);
    Route::delete('/news/{id}/urls/{urlId}', ['uses' => 'NewsController@destroyUrl']);

});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/grid/edit', ['uses' => 'GridEditController@view']);
    Route::get('grid/news/', ['uses' => 'GridEditController@index']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard/bulletins', ['uses' => 'BulletinController@index']);
    Route::get('/dashboard/bulletins/{id}/order', ['uses' => 'BulletinController@newsOrder']);

    Route::post('/bulletins', ['uses' => 'BulletinController@store']);
    Route::post('/bulletins/{id}/order', ['uses' => 'BulletinController@saveNewsOrder']);
    Route::post('/bulletins/{id}/send', ['uses' => 'BulletinController@sendToClients']);
    Route::post('/bulletins/{id}/send/test', ['uses' => 'BulletinController@sendToTestClient']);
    Route::delete('/bulletins/{id}', ['uses' => 'BulletinController@destroy']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/clients', ['uses' => 'ClientController@index']);
    Route::get('dashboard/clients/create', ['uses' => 'ClientController@create']);
    Route::get('dashboard/clients/{id}/edit', ['uses' => 'ClientController@edit']);

    Route::get('/clients', ['uses' => 'ClientController@indexJson']);
    Route::post('/clients', ['uses' => 'ClientController@store']);
    Route::put('/clients/{id}', ['uses' => 'ClientController@update']);
    Route::delete('/clients/{id}', ['uses' => 'ClientController@destroy']);
    Route::post('/clients/{id}/contacts', ['uses' => 'ClientController@storeContact']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/media', ['uses' => 'MediaController@index']);
    Route::get('dashboard/media/create', ['uses' => 'MediaController@create']);
    Route::get('dashboard/media/{id}/edit', ['uses' => 'MediaController@edit']);

    Route::post('/media', ['uses' => 'MediaController@store']);
    Route::put('/media/{id}', ['uses' => 'MediaController@update']);
    Route::delete('/media/{id}', ['uses' => 'MediaController@destroy']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/topics', ['uses' => 'TopicController@index']);
    Route::get('dashboard/topics/create', ['uses' => 'TopicController@create']);
    Route::get('dashboard/topics/{id}/edit', ['uses' => 'TopicController@edit']);

    Route::post('/topics', ['uses' => 'TopicController@store']);
    Route::put('/topics/{id}', ['uses' => 'TopicController@update']);
    Route::delete('/topics/{id}', ['uses' => 'TopicController@destroy']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/export', ['uses' => 'ExportController@index']);
    Route::post('dashboard/export', ['uses' => 'ExportController@export']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/reports', ['uses' => 'ReportController@index']);
    Route::get('/reports', ['uses' => 'ReportController@getReport']);
    Route::post('/reports/export', ['uses' => 'ReportController@exportReport']);
    // Route::get('/reports/export/check', ['uses' => 'ReportController@checkReport']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/users', ['uses' => 'UserController@index']);
    Route::get('dashboard/users/create', ['uses' => 'UserController@create']);
    Route::get('dashboard/users/{id}/edit', ['uses' => 'UserController@edit']);

    Route::post('/users', ['uses' => 'UserController@store']);
    Route::put('/users/{id}', ['uses' => 'UserController@update']);
    Route::delete('/users/{id}', ['uses' => 'UserController@destroy']);
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('dashboard/custom/subtitles', ['uses' => 'CustomizeController@subtitles']);
    Route::get('/custom/subtitles/{clientId}', ['uses' => 'CustomizeController@getSubtitlesByClient']);
    Route::post('/custom/subtitles/{clientId}', ['uses' => 'CustomizeController@saveSubtitles']);
});

Route::get('foo', function() {
    $a = new App\Http\Controllers\ReportGenerator();
    return $a->report5('2015-09-01', '2015-09-30', 101, 1);
});
