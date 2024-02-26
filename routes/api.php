<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\BeatController;
use App\Http\Controllers\Api\CheckinController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CustomController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpensesTypeController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VisitReportController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ReportingActivityController;
use App\Http\Controllers\Api\TourPlanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*================= Auth Routes ============================*/
Route::post('login', [LoginController::class, 'login']);
Route::post('customerLogin', [LoginController::class, 'customerLogin']);
Route::post('customerSignup', [LoginController::class, 'customerSignup']);
Route::any('getCategoryList', [ ProductController::class, 'getCategoryList']);
Route::any('getSubCategoryList', [ ProductController::class, 'getSubCategoryList']);
Route::any('getProductList', [ ProductController::class, 'getProductList']);
Route::any('getProductDetails', [ ProductController::class, 'getProductDetails']);
Route::any('getGiftList', [ ProductController::class, 'getGiftList']);
Route::any('getCategoryData', [ ProductController::class, 'getCategoryData']);
Route::any('getSubCategoryData', [ ProductController::class, 'getSubCategoryData']);
Route::any('getStateList', [ AddressController::class, 'getStateList']);
Route::any('getDistrictList', [ AddressController::class, 'getDistrictList']);
Route::any('getCityList', [ AddressController::class, 'getCityList']);
Route::any('getPincodeList', [ AddressController::class, 'getPincodeList']);
Route::any('getCustomerTypeList', [ CustomController::class, 'getCustomerTypeList']);
Route::any('getPincodeInfo', [ AddressController::class, 'getPincodeInfo']);
Route::any('getReportType', [ CustomController::class, 'getReportType']);
Route::any('getWorkType', [ CustomController::class, 'getWorkType']);
Route::any('getDevision', [ CustomController::class, 'getDevision']);

Route::any('mobileNumberExists', [ CustomController::class, 'mobileNumberExists']);
Route::any('gstNumberExists', [ CustomController::class, 'gstNumberExists']);
Route::any('getRetailerList', [ CustomController::class, 'getRetailerList']);

Route::any('emailExists', [ CustomController::class, 'emailExists']);
/*================= Customer Routes ============================*/
Route::group(['middleware' => ['auth:customers']], function () {
    Route::any('customer/getProfile', [LoginController::class, 'getCustomerProfile']);
    Route::any('customer/updateProfile', [LoginController::class, 'updateCustomerProfile']);
    // Dashboard
    Route::any('customer/dashboard', [DashboardController::class, 'customerDashboard']);
    // Secondry Sales
    Route::post('customer/insertSales', [SalesController::class, 'customerInsertSales']);
    Route::any('customer/getSales', [SalesController::class, 'customerGetSales']);
    Route::any('customer/getSalesDetails', [SalesController::class, 'customerSalesDetails']);
    Route::any('customer/approveSales', [SalesController::class, 'customerApproveSales']);
    Route::any('customer/rejectSales', [SalesController::class, 'customerRejectSales']);
    // Get Customer list
    Route::any('customer/parentCustomers', [CustomerController::class, 'customerParentCustomers']);
    Route::any('customer/getRetailers', [CustomerController::class, 'customerRetailers']);
    // Order Master
    Route::post('customer/insertOrder', [OrderController::class, 'customerInsertOrder']);
    Route::any('customer/getOrderList', [OrderController::class, 'customerOrderList']);
    Route::any('customer/getOrderDetails', [OrderController::class, 'customerOrderDetails']);
    //Coupon Scan
    Route::post('customer/couponScans', [CouponController::class, 'customerCouponScans']);
    Route::post('customer/getScanedCoupons', [CouponController::class, 'customerScanedCouponList']);
    Route::post('customer/pointRedemption', [WalletController::class, 'customerpointRedemption']);
});

Route::group(['middleware' => ['auth:users']], function () {
    // Dashboard
    Route::any('dashboard', [ DashboardController::class, 'dashboard']);
    
    Route::any('getProfile', [ LoginController::class, 'getProfile']);
    Route::post('updateProfile', [ LoginController::class, 'updateProfile']);
    Route::any('logout', [ LoginController::class, 'logout']);
    
    // Customer
    Route::post('storeCustomer', [ CustomerController::class, 'storeCustomer']);
    Route::post('updateCustomerLocation', [ CustomerController::class, 'updateCustomerLocation']);
    Route::post('updateCustomerProfile', [ CustomerController::class, 'updateCustomerProfile']);
    Route::any('getRetailers', [ CustomerController::class, 'getRetailers']);
    Route::any('getDistributors', [ CustomerController::class, 'getDistributors']);
    Route::any('getCustomerList', [ CustomerController::class, 'getCustomerList']);
    Route::any('getCustomerInfo', [ CustomerController::class, 'getCustomerInfo']);
    Route::post('leadToCustomer', [ CustomerController::class, 'leadToCustomer']);
    // Get Order List
    Route::post('insertOrder', [ OrderController::class, 'insertOrder']);
    Route::any('getOrderList', [ OrderController::class, 'getOrderList']);
    Route::any('getOrderDetails', [ OrderController::class, 'getOrderDetails']);
    Route::post('addCartItems', [ OrderController::class, 'addCartItems']);
    Route::get('getCartItems', [ OrderController::class, 'getCartItems']);
    Route::any('getBeatList', [ BeatController::class, 'getBeatList']);
    Route::any('getBeatDropdownList', [ BeatController::class, 'getBeatDropdownList']);
    Route::any('getBeatCustomers', [ BeatController::class, 'getBeatCustomers']);
    Route::post('userPunchin', [ AttendanceController::class, 'userPunchin']);
    Route::post('userPunchout', [ AttendanceController::class, 'userPunchout']);
    Route::any('getPunchin', [ AttendanceController::class, 'getPunchin']);
    Route::any('getAllUserPunchInOut', [ AttendanceController::class, 'getAllUserPunchInOut']);
    Route::any('attendance/changeStatus', [ AttendanceController::class, 'changeStatus']);
    Route::any('showAttendance', [ AttendanceController::class, 'showAttendance']);
    Route::any('lastPunchin', [ AttendanceController::class, 'lastPunchin']);
    Route::post('submitCheckin', [ CheckinController::class, 'submitCheckin']);
    Route::post('submitCheckout', [ CheckinController::class, 'submitCheckout']);
    Route::any('getCheckin', [ CheckinController::class, 'getCheckin']);
    Route::post('submitVisitReports', [ VisitReportController::class, 'submitVisitReports']);
    Route::any('getVisitTypes', [ VisitReportController::class, 'getVisitTypes']);
    Route::any('getVisitReports', [ VisitReportController::class, 'getVisitReports']);
    // Get Sales
    Route::post('insertSales', [ SalesController::class, 'insertSales']);
    Route::post('couponScans', [ CouponController::class, 'couponScans']);
    Route::any('getSales', [ SalesController::class, 'getSales']);
    Route::any('getSalesDetails', [ SalesController::class, 'getSalesDetails']);
    Route::any('getSurveyQuestions', [ SurveyController::class, 'getSurveyQuestions']);
    Route::any('getUnpaidInvoice', [ PaymentController::class, 'getUnpaidInvoice']);
    Route::post('paymentReceived', [ PaymentController::class, 'paymentReceived']);
    Route::any('getPaymentList', [ PaymentController::class, 'getPaymentList']);
    Route::any('getPaymentInfo', [ PaymentController::class, 'getPaymentInfo']);
    Route::post('createNewTask', [ UserController::class, 'createNewTask']);
    Route::post('taskMarkComplite', [ UserController::class, 'taskMarkComplite']);
    Route::post('getTaskInfo', [ UserController::class, 'getTaskInfo']);
    Route::any('getUpcomingTasks', [ UserController::class, 'getUpcomingTasks']);
    Route::any('updateLiveLocation', [ UserController::class, 'updateLiveLocation']);
    Route::any('addTourProgramme', [ UserController::class, 'addTourProgramme']);
    Route::any('upcommingTourProgramme', [ UserController::class, 'upcommingTourProgramme']);
    Route::any('userCityList', [ UserController::class, 'userCityList']);
    Route::post('userScheduleBeat', [ BeatController::class, 'userScheduleBeat']);
    Route::post('pointsCollection', [ WalletController::class, 'pointsCollection']);
    Route::any('getCollectedPoints', [ WalletController::class, 'getCollectedPoints']);
    Route::any('getUserActivity', [ UserController::class, 'getUserActivity']);
    Route::any('requestReport', [ UserController::class, 'requestReport']);
    Route::any('getNotification', [ UserController::class, 'getNotification']);
    Route::any('masterStateCity', [ UserController::class, 'masterStateCity']);
    Route::any('getPunchinMasterData', [ UserController::class, 'getPunchinMasterData']);
    //Reporting Activity
    Route::get('reporting/users', [ReportingActivityController::class, 'allReportingUsers']);
    Route::get('user/activity', [ReportingActivityController::class, 'userActivity']);
    //Tour Plan
    Route::get('tour/userlist', [TourPlanController::class, 'user_list']);
    Route::get('tour/show', [TourPlanController::class, 'show']);
    Route::post('tour/add', [TourPlanController::class, 'add']);
    Route::post('tour/edit', [TourPlanController::class, 'edit']);
    //Expenses Type
     Route::get('/getExpensesType', [ExpensesTypeController::class, 'getExpensesType']);
     Route::post('createExpense', [ExpensesTypeController::class, 'createExpense']);
     Route::get('expenseListing', [ExpensesTypeController::class, 'expenseListing']);
     Route::post('expenseDetails', [ExpensesTypeController::class, 'expenseDetails']);
     Route::post('updateExpense', [ExpensesTypeController::class, 'updateExpense']);
});


