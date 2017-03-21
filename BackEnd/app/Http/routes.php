<?php


/*
--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::controller("/test","Test");
Route::group(['prefix' => 'wechat'], function(){
    Route::controller("/wechat","Wechat\Wechat"); // 微信授权

    Route::group(['middleware' => 'AuthOfWechat'], function(){
        // 将微信openid与student的记录绑定
        Route::controller("/register","Wechat\Register");

        // 对student表的操作
        Route::controller("/account","Wechat\Account");
        Route::controller("/direction","Wechat\SelectDirection");
        Route::controller("/classes","Wechat\SelectClasses");

        // 对select_course表的操作
        Route::controller("/course","Wechat\CourseTab");

        Route::controller("/handle-course","Wechat\HandleCourse");

        Route::controller("/select-result","Wechat\SelectResult");
    });
});

Route::group(['prefix' => 'admin'], function(){
    // 管理员的接口走这里
    Route::controller("/login","Admin\Login"); // 管理端登录
    Route::group(['middleware' => 'AuthOfAdmin'], function(){
        // 加个中间件认证 
        Route::controller("/home","Admin\HomePage");
        Route::controller("/student","Admin\StudentPage");
        Route::controller("/classes","Admin\ClassesPage");
        Route::controller("/course","Admin\CoursePage");
        Route::controller("/info","Admin\InfoPage");
    });
});
