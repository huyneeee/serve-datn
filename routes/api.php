<?php

use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CommentDepartureController;
use App\Http\Controllers\Admin\CommentNewsController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeparturesController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NewCategoryController;
use App\Http\Controllers\Admin\NewController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NewPasswordController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Client\ClientNotificationController;
use App\Http\Controllers\Client\CommentCustomerController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\CustommerController;
use App\Http\Controllers\Client\EmailVerificationController;
use App\Http\Controllers\Client\PageController;
use App\Http\Controllers\Client\PaymentsController;
use App\Http\Controllers\Client\ResetPasswordCustomerController;
use App\Http\Controllers\MailController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('admin')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [LoginController::class, 'register']);
    Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
    Route::post('reset-password', [NewPasswordController::class, 'reset']);
    Route::post('client/update/{id}', [ClientController::class, 'update'])->middleware('can:client-edit');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::prefix('users')->group(function () {
            Route::get('index', [UserController::class, 'index'])->middleware('can:user-list');
            Route::get('show/{id}', [UserController::class, 'show'])->middleware('can:user-show');
            Route::post('store', [UserController::class, 'store'])->middleware('can:user-add');
            Route::post('update-role/{id}', [UserController::class, 'update_role'])->middleware('can:user-edit');
            Route::post('update/{id}', [UserController::class, 'update'])->middleware('can:user-edit');
            Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->middleware('can:user-force');
            Route::delete('delete/{id}', [UserController::class, 'destroy'])->middleware('can:user-delete');
            Route::get('view-delete', [UserController::class, 'viewDelete'])->middleware('can:user-viewDelete');
            Route::delete('delete-checked/{id}', [UserController::class, 'deleteChecked'])->middleware('can:user-deleteChecked');
            Route::get('restore/{id}', [UserController::class, 'restore'])->middleware('can:user-restore');
            Route::get('restore-all', [UserController::class, 'restoreAll'])->middleware('can:user-restoreAll');
        });
        Route::prefix('roles')->group(function () {
            Route::get('index', [RoleController::class, 'index'])->middleware('can:role-list');
            Route::get('show/{id}', [RoleController::class, 'show'])->middleware('can:role-show');
            Route::get('create', [RoleController::class, 'create']);
            Route::post('store', [RoleController::class, 'store'])->middleware('can:role-add');
            Route::get('edit/{id}', [RoleController::class, 'edit']);
            Route::post('update/{id}', [RoleController::class, 'update'])->middleware('can:role-edit');
            Route::delete('delete/{id}', [RoleController::class, 'destroy'])->middleware('can:role-delete');
            Route::get('view-delete', [RoleController::class, 'viewDelete'])->middleware('can:role-viewDelete');
            Route::delete('delete-checked/{id}', [RoleController::class, 'deleteChecked'])->middleware('can:role-deleteChecked');
            Route::get('restore/{id}', [RoleController::class, 'restore'])->middleware('can:role-restore');
            Route::get('restore-all', [RoleController::class, 'restoreAll'])->middleware('can:role-restoreAll');
        });
        Route::prefix('cars')->group(function () {
            Route::get('index', [CarController::class, 'index'])->middleware('can:car-list');
            Route::get('show/{id}', [CarController::class, 'show'])->middleware('can:car-show');
            Route::post('store', [CarController::class, 'store'])->middleware('can:car-add');
            Route::post('update/{id}', [CarController::class, 'update'])->middleware('can:car-edit');
            Route::delete('force-delete/{id}', [CarController::class, 'forceDelete'])->middleware('can:car-forceDelete');
            Route::delete('delete/{id}', [CarController::class, 'destroy'])->middleware('can:car-delete');
            Route::get('view-delete', [CarController::class, 'viewDelete'])->middleware('can:car-viewDelete');
            Route::delete('delete-checked/{id}', [CarController::class, 'deleteChecked'])->middleware('can:car-deleteChecked');
            Route::get('restore/{id}', [CarController::class, 'restore'])->middleware('can:car-restore');
            Route::get('restore-all', [CarController::class, 'restoreAll'])->middleware('can:car-restoreAll');
        });
        Route::prefix('departures')->group(function () {
            Route::get('index', [DeparturesController::class, 'index'])->middleware('can:departure-list');
            Route::get('detail-invoice/{id}', [DeparturesController::class, 'departure_invoice']);
            Route::get('getDataRoleCar', [DeparturesController::class, 'getDataRoleCar']);
            Route::get('show/{id}', [DeparturesController::class, 'show'])->middleware('can:departure-show');
            Route::post('store', [DeparturesController::class, 'store'])->middleware('can:departure-add');
            Route::post('update/{id}', [DeparturesController::class, 'update'])->middleware('can:departure-edit');
            Route::delete('force-delete/{id}', [DeparturesController::class, 'forceDelete'])->middleware('can:departure-forceDelete');
            Route::delete('delete/{id}', [DeparturesController::class, 'destroy'])->middleware('can:departure-delete');
            Route::get('view-delete', [DeparturesController::class, 'viewDelete'])->middleware('can:departure-viewDelete');
            Route::delete('delete-checked/{id}', [DeparturesController::class, 'deleteChecked'])->middleware('can:departure-deleteChecked');
            Route::get('restore/{id}', [DeparturesController::class, 'restore'])->middleware('can:departure-restore');
            Route::get('restore-all', [DeparturesController::class, 'restoreAll'])->middleware('can:departure-restoreAll');
        });
        Route::prefix('policies')->group(function () {
            Route::get('index', [PolicyController::class, 'index'])->middleware('can:policies-list');
            Route::get('show/{id}', [PolicyController::class, 'show'])->middleware('can:policies-show');
            Route::post('store', [PolicyController::class, 'store'])->middleware('can:policies-add');
            Route::post('update/{id}', [PolicyController::class, 'update'])->middleware('can:policies-edit');
            Route::delete('delete/{id}', [PolicyController::class, 'destroy'])->middleware('can:policies-delete');
            Route::delete('force-delete/{id}', [PolicyController::class, 'forceDelete'])->middleware('can:policies-forceDelete');
            Route::get('view-delete', [PolicyController::class, 'viewDelete'])->middleware('can:policies-viewDelete');
            Route::delete('delete-checked/{id}', [PolicyController::class, 'deleteChecked'])->middleware('can:policies-deleteChecked');
            Route::get('restore/{id}', [PolicyController::class, 'restore'])->middleware('can:policies-restore');
            Route::get('restore-all', [PolicyController::class, 'restoreAll'])->middleware('can:policies-restoreAll');
        });
        Route::prefix('new_category')->group(function () {
            Route::get('index', [NewCategoryController::class, 'index'])->middleware('can:newCategory-list');
            Route::get('parent', [NewCategoryController::class, 'parent']);
            Route::get('show/{id}', [NewCategoryController::class, 'show'])->middleware('can:newCategory-show');
            Route::post('store', [NewCategoryController::class, 'store'])->middleware('can:newCategory-add');
            Route::post('update/{id}', [NewCategoryController::class, 'update'])->middleware('can:newCategory-edit');
            Route::delete('delete/{id}', [NewCategoryController::class, 'destroy'])->middleware('can:newCategory-delete');
            Route::delete('force-delete/{id}', [NewCategoryController::class, 'forceDelete'])->middleware('can:newCategory-forceDelete');
            Route::get('view-delete', [NewCategoryController::class, 'viewDelete'])->middleware('can:newCategory-viewDelete');
            Route::delete('delete-checked/{id}', [NewCategoryController::class, 'deleteChecked'])->middleware('can:newCategory-deleteChecked');
            Route::get('restore/{id}', [NewCategoryController::class, 'restore'])->middleware('can:newCategory-restore');
            Route::get('restore-all', [NewCategoryController::class, 'restoreAll'])->middleware('can:newCategory-restoreAll');
        });
        Route::prefix('news')->group(function () {
            Route::get('index', [NewController::class, 'index'])->middleware('can:News-list');
            Route::get('parent', [NewController::class, 'parent']);
            Route::get('show/{id}', [NewController::class, 'show'])->middleware('can:News-show');
            Route::post('store', [NewController::class, 'store'])->middleware('can:News-add');
            Route::post('update/{id}', [NewController::class, 'update'])->middleware('can:News-edit');
            Route::delete('delete/{id}', [NewController::class, 'destroy'])->middleware('can:News-delete');
            Route::delete('force-delete/{id}', [NewController::class, 'forceDelete'])->middleware('can:News-forceDelete');
            Route::get('view-delete', [NewController::class, 'viewDelete'])->middleware('can:News-viewDelete');
            Route::delete('delete-checked/{id}', [NewController::class, 'deleteChecked'])->middleware('can:News-deleteChecked');
            Route::get('restore/{id}', [NewController::class, 'restore'])->middleware('can:News-restore');
            Route::get('restore-all', [NewController::class, 'restoreAll'])->middleware('can:News-restoreAll');
        });
        Route::prefix('comment-news')->group(function () {
            Route::get('index', [CommentNewsController::class, 'index'])->middleware('can:comment-news-list');
            Route::get('show/{id}', [CommentNewsController::class, 'show'])->middleware('can:comment-news-show');
            Route::post('update/{id}', [CommentNewsController::class, 'update'])->middleware('can:comment-news-edit');
            Route::delete('delete/{id}', [CommentNewsController::class, 'destroy'])->middleware('can:comment-news-delete');
            Route::delete('force-delete/{id}', [CommentNewsController::class, 'forceDelete'])->middleware('can:comment-news-forceDelete');
            Route::get('view-delete', [CommentNewsController::class, 'viewDelete'])->middleware('can:comment-news-viewDelete');
            Route::delete('delete-checked/{id}', [CommentNewsController::class, 'deleteChecked'])->middleware('can:comment-news-deleteChecked');
            Route::get('restore/{id}', [CommentNewsController::class, 'restore'])->middleware('can:comment-news-restore');
            Route::get('restore-all', [CommentNewsController::class, 'restoreAll'])->middleware('can:comment-news-restoreAll');
        });
        Route::prefix('comment-departure')->group(function () {
            Route::get('index', [CommentDepartureController::class, 'index'])->middleware('can:comment-departure-list');
            Route::get('show/{id}', [CommentDepartureController::class, 'show'])->middleware('can:comment-departure-show');
            Route::post('update/{id}', [CommentDepartureController::class, 'update'])->middleware('can:comment-departure-edit');
            Route::delete('delete/{id}', [CommentDepartureController::class, 'destroy'])->middleware('can:comment-departure-delete');
            Route::delete('force-delete/{id}', [CommentDepartureController::class, 'forceDelete'])->middleware('can:comment-departure-forceDelete');
            Route::get('view-delete', [CommentDepartureController::class, 'viewDelete'])->middleware('can:comment-departure-viewDelete');
            Route::delete('delete-checked/{id}', [CommentDepartureController::class, 'deleteChecked'])->middleware('can:comment-departure-deleteChecked');
            Route::get('restore/{id}', [CommentDepartureController::class, 'restore'])->middleware('can:comment-departure-restore');
            Route::get('restore-all', [CommentDepartureController::class, 'restoreAll'])->middleware('can:comment-departure-restoreAll');
        });
        Route::prefix('client')->group(function () {
            Route::get('index', [ClientController::class, 'index'])->middleware('can:client-list');
            Route::get('show/{id}', [ClientController::class, 'show'])->middleware('can:client-show');
            Route::delete('delete/{id}', [ClientController::class, 'destroy'])->middleware('can:client-delete');
            Route::delete('force-delete/{id}', [ClientController::class, 'forceDelete'])->middleware('can:client-forceDelete');
            Route::get('view-delete', [ClientController::class, 'viewDelete'])->middleware('can:client-viewDelete');
            Route::delete('delete-checked/{id}', [ClientController::class, 'deleteChecked'])->middleware('can:client-deleteChecked');
            Route::get('restore/{id}', [ClientController::class, 'restore'])->middleware('can:client-restore');
            Route::get('restore-all', [ClientController::class, 'restoreAll'])->middleware('can:client-restoreAll');
        });
        Route::prefix('contacts')->group(function () {
            Route::get('index', [AdminContactController::class, 'index']);
            // Route::get('show/{id}', [AdminContactController::class, 'show']);
            // Route::delete('delete/{id}', [AdminContactController::class, 'destroy']);
            // Route::delete('force-delete/{id}', [AdminContactController::class, 'forceDelete']);
            // Route::get('view-delete', [AdminContactController::class, 'viewDelete']);
            // Route::delete('delete-checked/{id}', [AdminContactController::class, 'deleteChecked']);
            // Route::get('restore/{id}', [AdminContactController::class, 'restore']);
            // Route::get('restore-all', [AdminContactController::class, 'restoreAll']);
        });
        Route::prefix('invoices')->group(function () {
            Route::get('index', [InvoiceController::class, 'index']);
            Route::get('detail/{id}', [InvoiceController::class, 'invoice_detail']);
            Route::post('update/{id}', [InvoiceController::class, 'update_invoice']);
            // Route::get('show/{id}', [AdminContactController::class, 'show']);
            Route::delete('delete/{id}', [InvoiceController::class, 'destroy']);
            Route::delete('force-delete/{id}', [InvoiceController::class, 'forceDelete']);
            Route::get('view-delete', [InvoiceController::class, 'viewDelete']);
            Route::delete('delete-checked/{id}', [InvoiceController::class, 'deleteChecked']);
            Route::get('restore/{id}', [InvoiceController::class, 'restore']);
            Route::get('restore-all', [InvoiceController::class, 'restoreAll']);
        });
        Route::prefix('dashboard')->group(function () {
            Route::get('index', [DashboardController::class, 'index']);
            Route::get('date-from', [DashboardController::class, 'date_from']);
            Route::get('date-month', [DashboardController::class, 'date_month']);
        });
    });
});
Route::prefix('client')->group(function () {
    Route::prefix('customers')->group(function () {
        Route::post('register', [CustommerController::class, 'register']);
        Route::post('login', [CustommerController::class, 'login']);
        Route::get('show/{id}', [CustommerController::class, 'show']);
        Route::post('update-login/{id}', [CustommerController::class, 'updateLogin']);
        //update password customer
        Route::post('update-password/{id}', [CustommerController::class, 'updatePassword']);
        Route::post('logout', [CustommerController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
        Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');
        Route::post('forgot-password', [ResetPasswordCustomerController::class, 'forgotPassword']);
        Route::post('reset-password', [ResetPasswordCustomerController::class, 'reset']);
        //bình luận tin tức
        Route::post('comment-new/{id}', [CommentCustomerController::class, 'commentNew'])->middleware('auth:sanctum');
        //hiển thị bình luận tin tức
        Route::get('view-comment-new', [CommentCustomerController::class, 'viewCommentNew'])->middleware('auth:sanctum');
        //bình luận chuyến
        Route::post('comment-departure/{id}', [CommentCustomerController::class, 'commentDeparture'])->middleware('auth:sanctum');
        //hiển thị bình luận chuyến
        Route::get('view-comment-departure/{id}', [PageController::class, 'view_comment_departure']);
        //show chuyến 
        //   Route::get('departure-all', [PageController::class, 'departureAll']);
        //update chuyến
        Route::post('update-departure/{id}', [PageController::class, 'updateDeparture']);
        //show chuyến theo bộ lọc
        Route::get('departure-filter', [PageController::class, 'departureFilter']);
        //show invoice theo mã
        Route::get('invoice-code-filter', [PageController::class, 'invoiceCodeFilter']);
        //trang liên hệ
        Route::post('contact-add', [ContactController::class, 'contactAdd']);
        Route::prefix('invoice')->group(function () {
            Route::post('add-invoice/{id}', [PageController::class, 'addInvoice'])->middleware('auth:sanctum');
            Route::get('history-invoice', [PageController::class, 'historyInvoice'])->middleware('auth:sanctum');
        });
        Route::prefix('notification')->group(function () {
            Route::get('index', [ClientNotificationController::class, 'index']);
            Route::post('store', [ClientNotificationController::class, 'store'])->middleware('auth:sanctum');
            Route::post('update/{id}', [ClientNotificationController::class, 'update'])->middleware('auth:sanctum');
            Route::get('customer/{id}', [ClientNotificationController::class, 'customer'])->middleware('auth:sanctum');
            Route::get('customer-count/{id}', [ClientNotificationController::class, 'customerCount'])->middleware('auth:sanctum');
        });
    });
    Route::post('create', [PaymentsController::class, 'create'])->middleware('auth:sanctum');
    Route::get('return-vnpay', [PaymentsController::class, 'vnpay_return'])->name('payment.return');
});
