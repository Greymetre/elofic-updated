<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BeatController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\FirmTypeController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SchemeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserReportingController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VisitReportController;
use App\Http\Controllers\VisitTypeController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DesignationController;
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

Route::get('/', function () { return view('auth.login'); });

Route::get('aboutus', function () { return view('aboutus'); });
Route::get('aboutus/mission', function () { return view('mission'); });
Route::get('aboutus/consulting', function () { return view('consulting'); });
Route::get('aboutus/abridgemspl', function () { return view('abridgemspl'); });
Route::get('aboutus/abridgeit', function () { return view('abridgeit'); });
Route::get('contactus', function () { return view('contactus'); });
Route::get('/home', [ HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    //Dashboard
    Route::get('dashboard', [ DashboardController::class, 'index']);
    Route::post('dashboardData', [ DashboardController::class, 'dashboardData']);
    Route::post('travelSummaryData', [ DashboardController::class, 'travelSummaryData']);
    Route::post('visitSummaryData', [ DashboardController::class, 'visitSummaryData']);
    Route::post('couponSummaryData', [ DashboardController::class, 'couponSummaryData']);
    Route::post('orderSummaryData', [ DashboardController::class, 'orderSummaryData']);
    Route::post('salesSummaryData', [ DashboardController::class, 'salesSummaryData']);
    Route::post('activityDashboardCount', [ DashboardController::class, 'activityDashboardCount']);
     //Customers
    Route::resource('customertype', CustomerTypeController::class);
    Route::post('customertype-active', [ CustomerTypeController::class, 'active'])->name('customertype.active');
    Route::resource('firmtype', FirmTypeController::class);
    Route::post('firmtype-active', [ FirmTypeController::class, 'active'])->name('firmtype.active');
    Route::resource('customers', CustomerController::class);
    Route::any('customers-download', [ CustomerController::class, 'download'])->name('customers.download');
    Route::any('customers-template', [ CustomerController::class, 'template'])->name('customers.template');
    Route::post('customers-upload', [ CustomerController::class, 'upload'])->name('customers.upload');
    Route::post('customers-active', [ CustomerController::class, 'active'])->name('customers.active');

    Route::any('customers-survey', [ CustomerController::class, 'survey'])->name('customers.survey');
    Route::any('survey-download', [ CustomerController::class, 'surveyDownload'])->name('survey-download');

    //
    Route::any('distributors', [ CustomerController::class, 'distributors'])->name('distributors.index');
    Route::any('distributors/create', [ CustomerController::class, 'createDistributor'])->name('distributors.create');
     Route::any('distributor-download', [ CustomerController::class, 'distributordownload'])->name('distributor-download');
     //Country
    Route::resource('country', CountryController::class);
    Route::post('country-active', [ CountryController::class, 'active'])->name('country.active');
    Route::any('country-download', [ CountryController::class, 'download'])->name('country.download');
    Route::any('country-template', [ CountryController::class, 'template'])->name('country.template');
    Route::post('country-upload', [ CountryController::class, 'upload'])->name('country.upload');

     //State
    Route::resource('state', StateController::class);
    Route::any('state-download', [ StateController::class, 'download'])->name('state.download');
    Route::any('state-template', [ StateController::class, 'template'])->name('state.template');
    Route::post('state-upload', [ StateController::class, 'upload'])->name('state.upload');
    Route::post('state-active', [ StateController::class, 'active'])->name('state.active');

     //District
    Route::resource('district', DistrictController::class);
    Route::any('district-download', [ DistrictController::class, 'download'])->name('district.download');
    Route::any('district-template', [ DistrictController::class, 'template'])->name('district.template');
    Route::post('district-upload', [ DistrictController::class, 'upload'])->name('district.upload');
    Route::post('district-active', [ DistrictController::class, 'active'])->name('district.active');
     //City
    Route::resource('city', CityController::class);
    Route::any('city-download', [ CityController::class, 'download'])->name('city.download');
    Route::any('city-template', [ CityController::class, 'template'])->name('city.template');
    Route::post('city-upload', [ CityController::class, 'upload'])->name('city.upload');
    Route::post('city-active', [ CityController::class, 'active'])->name('city.active');
     //Pincode
    Route::resource('pincode', PincodeController::class);
    Route::any('pincode-download', [ PincodeController::class, 'download'])->name('pincode.download');
    Route::any('pincode-template', [ PincodeController::class, 'template'])->name('pincode.template');
    Route::post('pincode-upload', [ PincodeController::class, 'upload'])->name('pincode.upload');
    Route::post('pincode-active', [ PincodeController::class, 'active'])->name('pincode.active');
    // Roles
    Route::delete('roles/destroy', [ RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);
    Route::any('roles-download', [ RolesController::class, 'download'])->name('roles.download');
    Route::any('roles-template', [ RolesController::class, 'template'])->name('roles.template');
    Route::post('roles-upload', [ RolesController::class, 'upload'])->name('roles.upload');
    //braches 
    Route::resource('branches', BranchController::class);
    //Division 
    Route::resource('division', DivisionController::class);
    //Designation 
    Route::resource('designation', DesignationController::class);
    // Users
    Route::delete('users/destroy', [ UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::resource('users', UsersController::class);
    Route::any('users-download', [ UsersController::class, 'download'])->name('users.download');
    Route::any('users-template', [ UsersController::class, 'template'])->name('users.template');
    Route::post('users-upload', [ UsersController::class, 'upload'])->name('users.upload');
    Route::post('users-active', [ UsersController::class, 'active'])->name('users.active');
    Route::any('usercity', [ UsersController::class, 'userCity'])->name('users.usercity');
    Route::post('usercity-upload', [ UsersController::class, 'userCityUpload'])->name('usercity.upload');
    Route::any('usercity-download', [ UsersController::class, 'userCitydownload'])->name('usercity.download');
    //Targets
    Route::resource('reportings', UserReportingController::class);
    //Appraisal
    Route::get('appraisal/create', [AppraisalController::class, 'create']);
    Route::get('appraisal/index', [AppraisalController::class, 'index'])->name('appraisal.index');
    Route::post('appraisal/store', [AppraisalController::class, 'store'])->name('appraisal.store');
    Route::post('appraisal/update', [AppraisalController::class, 'update'])->name('appraisal.update');
    // Permissions
    Route::delete('permissions/destroy', [ PermissionsController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', PermissionsController::class);
    Route::any('permissions-download', [ PermissionsController::class, 'download'])->name('permissions.download');
    Route::any('permissions-template', [ PermissionsController::class, 'template'])->name('permissions.template');
    Route::post('permissions-upload', [ PermissionsController::class, 'upload'])->name('permissions.upload');
    //Category Route
    Route::resource('categories', CategoryController::class);
    Route::any('categories-download', [ CategoryController::class, 'download'])->name('categories.download');
    Route::any('categories-template', [ CategoryController::class, 'template'])->name('categories.template');
    Route::post('categories-upload', [ CategoryController::class, 'upload'])->name('categories.upload');
    Route::post('categories-active', [ CategoryController::class, 'active'])->name('categories.active');
     //Sub Category
    Route::resource('subcategories', SubCategoryController::class);
    Route::any('subcategories-download', [ SubCategoryController::class, 'download'])->name('subcategories.download');
    Route::any('subcategories-template', [ SubCategoryController::class, 'template'])->name('subcategories.template');
    Route::post('subcategories-upload', [ SubCategoryController::class, 'upload'])->name('subcategories.upload');
    Route::post('subcategories-active', [ SubCategoryController::class, 'active'])->name('subcategories.active');
     //Brand
    Route::resource('brands', BrandController::class);
    Route::any('brands-download', [ BrandController::class, 'download'])->name('brands.download');
    Route::any('brands-template', [ BrandController::class, 'template'])->name('brands.template');
    Route::post('brands-upload', [ BrandController::class, 'upload'])->name('brands.upload');
    Route::post('brands-active', [ BrandController::class, 'active'])->name('brands.active');
     //UnitMeasure
    Route::resource('units', UnitController::class);
    Route::any('units-download', [ UnitController::class, 'download'])->name('units.download');
    Route::any('units-template', [ UnitController::class, 'template'])->name('units.template');
    Route::post('units-upload', [ UnitController::class, 'upload'])->name('units.upload');
    Route::post('units-active', [ UnitController::class, 'active'])->name('units.active');
     //Products
    Route::resource('products', ProductController::class);
    Route::any('products-download', [ ProductController::class, 'download'])->name('products.download');
    Route::any('products-template', [ ProductController::class, 'template'])->name('products.template');
    Route::post('products-upload', [ ProductController::class, 'upload'])->name('products.upload');
    Route::post('products-active', [ ProductController::class, 'active'])->name('products.active');
    Route::any('stockinfo', [ ProductController::class, 'stockInfo'])->name('products.stockinfo');
    Route::any('stockupdate', [ ProductController::class, 'stockUpdate'])->name('products.stockupdate');
    Route::any('production', [ ProductController::class, 'production'])->name('products.production');
    Route::any('productionupdate', [ ProductController::class, 'productionUpdate'])->name('products.productionupdate');
    Route::any('products-list', [ ProductController::class, 'productList']);
    //Orders
    Route::resource('orders', OrderController::class);
    Route::any('orders-download', [ OrderController::class, 'download'])->name('orders.download');
    Route::any('orders-template', [ OrderController::class, 'template'])->name('orders.template');
    Route::post('orders-upload', [ OrderController::class, 'upload'])->name('orders.upload');
    Route::post('orders-active', [ OrderController::class, 'active'])->name('orders.active');
    Route::any('ordersInfo', [ OrderController::class, 'ordersInfo'])->name('orders.info');
    Route::get('ordertopoint', [ OrderController::class, 'ordertopoint']);
     Route::any('expected-delivery', [ OrderController::class, 'expectedDelivery'])->name('orders.expecteddelivery');
    Route::any('submit-expected-delivery', [ OrderController::class, 'submitExpectedDelivery'])->name('orders.submitexpecteddelivery');
    Route::any('order-dispatched/{id}', [ OrderController::class, 'orderDispatched'])->name('orders.dispatched');
    Route::any('order-partially-dispatched/{id}', [ OrderController::class, 'orderPartiallyDispatched'])->name('orders.partiallydispatched');
    Route::post('submit-dispatched', [ OrderController::class, 'submitDispatched'])->name('orders.submitdispatched');
    //Targets
    Route::resource('targets', TargetController::class);
    //Secondry Sales
    Route::resource('sales', SalesController::class);
    Route::any('sales-download', [ SalesController::class, 'download'])->name('sales.download');
    Route::any('sales-template', [ SalesController::class, 'template'])->name('sales.template');
    Route::post('sales-upload', [ SalesController::class, 'upload'])->name('sales.upload');

    Route::any('saleApproval/{sales_id}', [ SalesController::class, 'saleApproval'])->name('sales.saleApproval');
    Route::post('sales-active', [ SalesController::class, 'active'])->name('sales.active');
    Route::any('salesInfo', [ SalesController::class, 'salesInfo'])->name('sales.info');
    //Schemes
    Route::resource('schemes', SchemeController::class);
    Route::any('schemes-download', [ SchemeController::class, 'download'])->name('schemes.download');
    Route::any('schemes-template', [ SchemeController::class, 'template'])->name('schemes.template');
    Route::post('schemes-upload', [ SchemeController::class, 'upload'])->name('schemes.upload');
    Route::post('schemes-active', [ SchemeController::class, 'active'])->name('schemes.active');
    //Redeemption Products
    Route::resource('gifts', GiftController::class);
    Route::any('gifts-download', [ GiftController::class, 'download'])->name('gifts.download');
    Route::any('gifts-template', [ GiftController::class, 'template'])->name('gifts.template');
    Route::post('gifts-upload', [ GiftController::class, 'upload'])->name('gifts.upload');
    Route::post('gifts-active', [ GiftController::class, 'active'])->name('gifts.active');
    //Wallets
    Route::resource('wallets', WalletController::class);
    Route::any('wallets-download', [ WalletController::class, 'download'])->name('wallets.download');
    Route::any('wallets-template', [ WalletController::class, 'template'])->name('wallets.template');
    Route::post('wallets-upload', [ WalletController::class, 'upload'])->name('wallets.upload');
    Route::post('wallets-active', [ WalletController::class, 'active'])->name('wallets.active');
    Route::any('redeemedPoint', [ WalletController::class, 'redeemedPoint'])->name('wallets.redeemedPoint');
    Route::any('walletsInfo', [ WalletController::class, 'walletsInfo'])->name('wallets.info');
    //Settings
    Route::get('settings', [ SettingController::class, 'index']);
    Route::any('settings-download', [ SettingController::class, 'download'])->name('settings.download');
    Route::any('settings-template', [ SettingController::class, 'template'])->name('settings.template');
    Route::post('settings-upload', [ SettingController::class, 'upload'])->name('settings.upload');

    Route::any('settingSubmit', [ SettingController::class, 'settingSubmit']);
    //Settings
    Route::resource('status', StatusController::class);
    Route::any('status-download', [ StatusController::class, 'download'])->name('status.download');
    Route::any('status-template', [ StatusController::class, 'template'])->name('status.template');
    Route::post('status-upload', [ StatusController::class, 'upload'])->name('status.upload');
    Route::post('status-active', [ StatusController::class, 'active'])->name('status.active');
    //CustmersLogin
    Route::resource('coupons', CouponsController::class);
    Route::any('coupons-download', [ CouponsController::class, 'download'])->name('coupons.download');
    Route::any('coupons-template', [ CouponsController::class, 'template'])->name('coupons.template');
    Route::post('coupons-upload', [ CouponsController::class, 'upload'])->name('coupons.upload');

    Route::any('couponprofile', [ CouponsController::class, 'couponprofile'])->name('coupons.couponprofile');
    //CustmersLogin
    Route::any('customersLogin', [ CustomerController::class, 'customersLogin'])->name('customers.customersLogin');
    //Beat
    Route::resource('beats', BeatController::class);
    Route::any('beatdetail', [ BeatController::class, 'beatdetail'])->name('beats.beatdetail');
    Route::any('beats-download', [ BeatController::class, 'download'])->name('beats.download');
    Route::any('beats-template', [ BeatController::class, 'template'])->name('beats.template');
    Route::post('beats-upload', [ BeatController::class, 'upload'])->name('beats.upload');
    Route::post('add-beatusers', [ BeatController::class, 'addBeatUsers'])->name('beats.add-beatusers');
    Route::post('add-beatcustomers', [ BeatController::class, 'addBeatCustomer'])->name('beats.add-beatcustomers');
    Route::delete('schedule-delete/{id}', [ BeatController::class, 'beatScheduleDelete']);
    Route::post('updateschedule', [ BeatController::class, 'beatScheduleUpdate']);
    Route::delete('beatcustomer-delete/{id}', [ BeatController::class, 'beatCustomerDelete']);
    Route::delete('beat-user-delete/{id}', [ BeatController::class, 'beatUserDelete']);
    Route::any('beats-schedule/{id}', [ BeatController::class, 'beatsSchedule']);
    //Current Location 
    Route::any('livelocation', [ BeatController::class, 'livelocation']);
    //Attendance
    Route::any('attendances', [ AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('submitAttendances', [ AttendanceController::class, 'submitAttendances'])->name('submitAttendances');
    Route::any('attendancesInfo', [ AttendanceController::class, 'attendancesInfo'])->name('attendances.info');
    Route::any('attendance-download', [ AttendanceController::class, 'download'])->name('attendance.download');
    Route::any('removePunchout', [ AttendanceController::class, 'removePunchout'])->name('removePunchout');
    Route::delete('attendances/{id}', [ AttendanceController::class, 'destroy'])->name('attendances.destroy');

    Route::any('approveAttendance', [ AttendanceController::class, 'approveAttendance'])->name('approveAttendance');
    Route::any('rejectAttendance', [ AttendanceController::class, 'rejectAttendance'])->name('rejectAttendance');


    
    //Checkin
    Route::any('checkin', [ CheckinController::class, 'index'])->name('checkin.index');
    Route::any('checkin-download', [ CheckinController::class, 'download'])->name('checkin.download');
    //Visit Type
    Route::resource('visittypes', VisitTypeController::class);
    Route::any('visittypes-download', [ VisitTypeController::class, 'download'])->name('visittypes.download');
    Route::any('visittypes-template', [ VisitTypeController::class, 'template'])->name('visittypes.template');
    Route::post('visittypes-upload', [ VisitTypeController::class, 'upload'])->name('visittypes.upload');
    Route::post('visittypes-active', [ VisitTypeController::class, 'active'])->name('visittypes.active');
    //Visit Reports
    Route::resource('visitreports', VisitReportController::class);
    Route::any('visitreports-download', [ VisitReportController::class, 'download'])->name('visitreports.download');
    Route::any('visitreports-template', [ VisitReportController::class, 'template'])->name('visitreports.template');
    Route::post('visitreports-upload', [ VisitReportController::class, 'upload'])->name('visitreports.upload');
    Route::post('visitreports-active', [ VisitReportController::class, 'active'])->name('visitreports.active');
    Route::any('mastervisitreport', [ VisitReportController::class, 'masterVisitreport']);
    Route::any('master-visitreports-download', [ VisitReportController::class, 'masterVisitreportsDownload']);
    //Notes
    Route::resource('notes', NotesController::class);
    Route::post('notes-active', [ NotesController::class, 'active'])->name('notes.active');
    Route::any('notes-download', [ NotesController::class, 'download'])->name('notes.download');
    //Tasks
    Route::resource('tasks', TasksController::class);
    Route::any('tasks-download', [ TasksController::class, 'download'])->name('tasks.download');
    Route::any('tasks-template', [ TasksController::class, 'template'])->name('tasks.template');
    Route::post('tasks-upload', [ TasksController::class, 'upload'])->name('tasks.upload');
    Route::post('tasks-active', [ TasksController::class, 'active'])->name('tasks.active');
    Route::any('tasks-completed', [ TasksController::class, 'completed'])->name('tasks.completed');
    Route::any('tasks-done', [ TasksController::class, 'done'])->name('tasks.done');
    Route::any('tasks-reopen', [ TasksController::class, 'reopen'])->name('tasks.reopen');
    Route::any('tasksInfo', [ TasksController::class, 'tasksInfo'])->name('tasks.info');
    //Payment
    Route::resource('payments', PaymentController::class);
    Route::any('payments-download', [ PaymentController::class, 'download'])->name('payments.download');
    Route::any('payments-template', [ PaymentController::class, 'template'])->name('payments.template');
    Route::post('payments-upload', [ PaymentController::class, 'upload'])->name('payments.upload');
    Route::any('paymentsInfo', [ PaymentController::class, 'paymentsInfo'])->name('payments.info');
    //Supports
    Route::resource('supports', SupportController::class);
    Route::any('supports-download', [ SupportController::class, 'download'])->name('supports.download');
    Route::any('supports-template', [ SupportController::class, 'template'])->name('supports.template');
    Route::post('supports-upload', [ SupportController::class, 'upload'])->name('supports.upload');
    Route::any('supports-assigned', [ SupportController::class, 'assigned'])->name('supports.assigned');
    Route::any('supports-response', [ SupportController::class, 'response'])->name('supports.response');
    Route::any('supports-message', [ SupportController::class, 'message'])->name('supports.message');
    Route::any('supports-closed', [ SupportController::class, 'closed'])->name('supports.closed');
    Route::any('supports-reopend', [ SupportController::class, 'reopend'])->name('supports.reopend');
    //Proposals
    Route::resource('proposals', ProposalController::class);
    Route::any('proposals-download', [ ProposalController::class, 'download'])->name('proposals.download');
    Route::any('proposals-template', [ ProposalController::class, 'template'])->name('proposals.template');
    Route::post('proposals-upload', [ ProposalController::class, 'upload'])->name('proposals.upload');
    Route::post('proposals-active', [ ProposalController::class, 'active'])->name('proposals.active');
    //Estimate
    Route::resource('estimates', EstimateController::class);
    Route::any('estimates-download', [ EstimateController::class, 'download'])->name('estimates.download');
    Route::any('estimates-template', [ EstimateController::class, 'template'])->name('estimates.template');
    Route::post('estimates-upload', [ EstimateController::class, 'upload'])->name('estimates.upload');
    Route::post('estimates-active', [ EstimateController::class, 'active'])->name('estimates.active');
    //DataSource
    Route::resource('datasources', DataSourceController::class);
    Route::any('datasources-download', [ DataSourceController::class, 'download'])->name('datasources.download');
    Route::any('datasources-template', [ DataSourceController::class, 'template'])->name('datasources.template');
    Route::post('datasources-upload', [ DataSourceController::class, 'upload'])->name('datasources.upload');
    //Shipments
    Route::resource('shipments', ShipmentController::class);
    Route::any('shipments-download', [ ShipmentController::class, 'download'])->name('shipments.download');
    Route::any('shipments-template', [ ShipmentController::class, 'template'])->name('shipments.template');
    Route::post('shipments-upload', [ ShipmentController::class, 'upload'])->name('shipments.upload');
    Route::post('shipments-active', [ ShipmentController::class, 'active'])->name('shipments.active');
    //Courier
    Route::resource('couriers', CourierController::class);
    Route::any('couriers-download', [ CourierController::class, 'download'])->name('couriers.download');
    Route::any('couriers-template', [ CourierController::class, 'template'])->name('couriers.template');
    Route::post('couriers-upload', [ CourierController::class, 'upload'])->name('couriers.upload');
    Route::post('couriers-active', [ CourierController::class, 'active'])->name('couriers.active');
    /*============= LeaveType ====================*/
    Route::resource('leavetypes', LeaveTypeController::class);
    /*============= Leave ====================*/
    Route::resource('leaves', LeaveController::class);
    Route::post('leaves-approved', [ LeaveController::class, 'approved'])->name('leaves.approved');
    Route::any('leaveapproval', [ LeaveController::class, 'leaveApproval'])->name('leaves.approval');
    Route::any('leaverejected', [ LeaveController::class, 'leaveRejected'])->name('leaves.rejected');
    /*============= Team ====================*/
    Route::resource('teams', TeamController::class);
    /*============= Holiday ====================*/
    Route::resource('holiday', HolidayController::class);
    Route::post('holiday-active', [ HolidayController::class, 'active'])->name('holiday.active');
    /*============= Meeting ====================*/
    Route::resource('meeting', MeetingController::class);
    Route::post('meeting-active', [ MeetingController::class, 'active'])->name('meeting.active');
    /*============= Awards ====================*/
    Route::resource('award', AwardController::class);
    Route::post('award-active', [ AwardController::class, 'active'])->name('award.active');
     /*============= Training ====================*/
    Route::resource('training', TrainingController::class);
    Route::post('training-active', [ TrainingController::class, 'active'])->name('training.active');
     /*============= Promotion ====================*/
    Route::resource('promotion', PromotionController::class);
    Route::post('promotion-active', [ PromotionController::class, 'active'])->name('promotion.active');
    /*============= Project ====================*/
    Route::resource('project', ProjectController::class);
    Route::post('project-active', [ ProjectController::class, 'active'])->name('project.active');
    /*============= Event ====================*/

     //Fields
    Route::resource('fields', FieldController::class);
    Route::post('fields-active', [ FieldController::class, 'active'])->name('fields.active');
    Route::any('contacts', [ ContactController::class, 'index'])->name('contacts.index');
    /*==== Reports ==========*/
    Route::any('reports/beatadherence', [ ReportController::class, 'beatadherence']);
    Route::any('reports/adherencesummary', [ ReportController::class, 'adherencesummary']);
    Route::any('reports/customervisit', [ ReportController::class, 'customervisit']);
    Route::any('counterVisitReportDownload', [ ReportController::class, 'counterVisitReportDownload']);
    Route::any('beatAdherenceDetailDownload', [ ReportController::class, 'beatAdherenceDetailDownload']);
    Route::any('reports/attendancereport', [ ReportController::class, 'attendancereport']);
    Route::any('reports/customersreport', [ ReportController::class, 'customersReport']);
    Route::any('reports/per_day_counter_visit_report', [ ReportController::class, 'perDayCounterVisitReport']);
    Route::any('reports/fieldactivity', [ ReportController::class, 'fieldActivity']);
    Route::any('fieldActivityReportData', [ ReportController::class, 'fieldActivityReportData']);
    Route::any('reports/tourprogramme', [ ReportController::class, 'tourProgramme']);
    Route::any('tourProgrammeReportData', [ ReportController::class, 'tourProgrammeReportData']);
    Route::any('reports/monthlymovement', [ ReportController::class, 'monthlyMovement']);
    Route::any('monthlyMovementReportData', [ ReportController::class, 'monthlyMovementReportData']);
    Route::any('reports/pointcollections', [ ReportController::class, 'pointCollections']);
    Route::any('pointCollectionReportData', [ ReportController::class, 'pointCollectionReportData']);
    Route::any('reports/territorycoverage', [ ReportController::class, 'territoryCoverage']);
    Route::any('territoryCoverageReportData', [ ReportController::class, 'territoryCoverageReportData']);
    Route::any('reports/performanceparameter', [ ReportController::class, 'performanceParameter']);
    Route::any('performanceParameterReportData', [ ReportController::class, 'performanceParameterReportData']);
    Route::any('reports/asmwisemechanicspoints', [ ReportController::class, 'asmWiseMechanicsPoints']);
    Route::any('asmWiseMechanicsPointsReportData', [ ReportController::class, 'asmWiseMechanicsPointsReportData']);
    Route::any('reports/targetvssales', [ ReportController::class, 'targetVsSales']);
    Route::any('targetvsSaleReportData', [ ReportController::class, 'targetvsSaleReportData']);
    Route::any('reports/surveyanalysis', [ ReportController::class, 'surveyAnalysis']);
    Route::any('surveyAnalysisReportData', [ ReportController::class, 'surveyAnalysisReportData']);
    Route::any('surveyAnalysis-download', [ ReportController::class, 'surveyAnalysisDownload']);

    Route::any('reports/gamification', [ ReportController::class, 'gamification'])->name('reports.gamification');
    Route::any('customerAnalysis-download', [ ReportController::class, 'customerAnalysisDownload']);
    //Report Download
    Route::any('fieldActivity-download', [ ReportController::class, 'fieldActivityDownload']);
    Route::any('tourProgramme-download', [ ReportController::class, 'tourProgrammeDownload']);
    Route::any('monthlyMovement-download', [ ReportController::class, 'monthlyMovementDownload']);
    Route::any('pointCollection-download', [ ReportController::class, 'pointCollectionDownload']);
    Route::any('territoryCoverage-download', [ ReportController::class, 'territoryCoverageDownload']);
    Route::any('performanceParameter-download', [ ReportController::class, 'performanceParameterDownload']);
    Route::any('mechanicsPoints-download', [ ReportController::class, 'mechanicsPointsDownload']);
    Route::any('targetAchievement-download', [ ReportController::class, 'targetAchievementDownload']);
    //Tours
    Route::resource('tours', TourController::class);
    Route::any('toursInfoUpdate', [ TourController::class, 'update'])->name('tours.toursInfoUpdate');
    Route::any('tours-download', [ TourController::class, 'download'])->name('tours.download');
    Route::any('tours-template', [ TourController::class, 'template'])->name('tourss.template');
    Route::post('tours-upload', [ TourController::class, 'upload'])->name('tours.upload');
    Route::post('tours-changeStatus', [ TourController::class, 'changeStatus'])->name('tours.changesttus');

    Route::get('logout', '\App\Http\Controllers\Auth\AuthenticatedSessionController@destroy');
});

    Route::any('getState', [ AjaxController::class, 'getState']);
    Route::any('getDistrict', [ AjaxController::class, 'getDistrict']);
    Route::any('getCity', [ AjaxController::class, 'getCity']);
    Route::any('getCountry', [ AjaxController::class, 'getCountry']);
    Route::any('getPincode', [ AjaxController::class, 'getPincode']);
    Route::any('getAddressData', [ AjaxController::class, 'getAddressData']);
    Route::any('getAddressInfo', [ AjaxController::class, 'getAddressInfo']);
    Route::any('getCustomerData', [ AjaxController::class, 'getCustomerData'])->name('getCustomerData');
    Route::any('getCategoryData', [ AjaxController::class, 'getCategoryData']);
    Route::any('getProductData', [ AjaxController::class, 'getProductData']);
    Route::any('getProductInfo', [ AjaxController::class, 'getProductInfo']);
    Route::any('getUserList', [ AjaxController::class, 'getUserList']);
    Route::any('getUserInfo', [ AjaxController::class, 'getUserInfo']);
    Route::any('getRetailerlist', [ AjaxController::class, 'getRetailerlist']);
    Route::any('getOrderInfo', [ AjaxController::class, 'getOrderInfo']);
    Route::any('uniqueValidation', [ AjaxController::class, 'uniqueValidation']);
    Route::any('getCustomerLatLong', [ AjaxController::class, 'getCustomerLatLong']);
    Route::any('getUppaidInvouces', [ AjaxController::class, 'getUppaidInvouces']);
    Route::any('dashboardActivity', [ AjaxController::class, 'dashboardActivity']);
    Route::any('getUserLocationData', [ AjaxController::class, 'getUserLocationData']);
    Route::any('getUserActivityData', [ AjaxController::class, 'getUserActivityData']);
    Route::any('getCustomerActivityData', [ AjaxController::class, 'getCustomerActivityData']);





//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

Route::get('/key-generate', function() {
    $exitCode = Artisan::call('key:generate');
    return '<h1>Cache key:generate</h1>';
});

Route::get('/taskreminder', function () {

    Artisan::call('task:reminder');
});


require __DIR__.'/auth.php';
