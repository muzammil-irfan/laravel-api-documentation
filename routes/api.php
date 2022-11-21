<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DenouncementController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestorePasswordController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\UserController;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::get('/listing', function () {
    return Listing::all();
});

Route::get("/listing/{id}", function($id){
    return Listing::find($id);
});
Route::post("/auth/login",[AuthController::class,"login"]);

Route::get("/sliders",[SliderController::class,"index"]);
Route::post("/restore-password", [RestorePasswordController::class, "store"]);
Route::post("/upload",[EvidenceController::class, "upload"]);
Route::get("/companies/{slug}", [CompanyController::class,"show"]);
Route::get("/external-denouncements/{slug}/{id}",[DenouncementController::class,"externalShow"]);
Route::get("/external-conversations/{id}",[ConversationController::class,"externalShow"]);
Route::get("/external-conversations",[ConversationController::class,"store"]);
Route::get("/external-categories/{id}",[CategoryController::class,"show"]);
Route::get("/external-sources/{id}", [SourceController::class, "show"]);
Route::get("/external-areas/{id}",[AreaController::class,"show"]);
Route::get("/external-offices/{id}",[OfficeController::class,"show"]);
Route::post('/external-denouncements', [DenouncementController::class,'store']);

Route::group(['middleware'=>["auth:sanctum"]],function() {
});
Route::resource("/external-denouncements",DenouncementController::class);
Route::middleware([ 'cors', 'auth:sanctum', 'temp-admin', 'enabled.user'])->group( function() {
    
    Route::resource("/countries",CompanyController::class); 
    Route::resource("/businesses",BusinessController::class);
    Route::resource("/sectors",SectorController::class);
    Route::resource("/evidences",EvidenceController::class);

    Route::resource("/login",LoginController::class);
    Route::get("/dashboard",[DashboardController::class,"index"]);
    Route::resource("/notifications",NotificationController::class);
    Route::resource("/reports",ReportController::class);
    Route::resource("/profile",ProfileController::class);
    
    Route::get('/users/email/{email}', [UserController::class,'show']);

    Route::middleware("company.user")->group(function() {
        Route::resource('/denouncements', DenouncementController::class);
        Route::resource('/conversations', ConversationController::class);
        Route::resource('/close-reasons', 'ClosingReasonController');
        Route::resource('/investigators', 'InvestigatorController');
        


        Route::resource('/company-users', 'CompanyUserController');
        Route::resource('/categories', CategoryController::class);
        Route::resource('/sources', SourceController::class);
        Route::resource('/areas', AreaController::class);
        Route::resource('/offices', OfficeController::class);
    });

    Route::group([],function() {
        Route::resource('/companies', CompanyController::class);
        Route::get('/companies/ruc/{ruc}', [CompanyController::class,'byRuc']);
        Route::resource('/users', UserController::class);
        Route::get('/free-users', [UserController::class,"free"]);

        Route::resource('/settings', SettingController::class);        
    });

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = Auth::user();

    return $user->company_id;
});
