<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\LoginController; // Add this for web guard login
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\EstateOwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TenancyTypeController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\VoidsController;
use App\Http\Controllers\AsbController;
use App\Http\Controllers\PestController;
use App\Http\Controllers\Location;
use App\Http\Controllers\FobController;
use App\Http\Controllers\ParkController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserManagerController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RegisteredController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionAccountController;
use App\Http\Controllers\BrandController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root login to admin login
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::prefix('admin')->group(function () {
    // Admin authentication routes
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('request/password', [AdminController::class, 'passwordReset'])->name('admin.password.reset');
    Route::post('request/password', [AdminController::class, 'emailPassword'])->name('admin.password.email');


    // Routes requiring admin authentication
    Route::middleware('auth:admin')->group(function () {
        // Admin dashboard
        Route::get('/dashboard', [HomeController::class, 'AdminIndex'])->name('admin.dashboard');
Route::middleware('subscription')->group(function () {  
        // Occupants (Tenant) routes
        Route::prefix('occupants')->group(function () {
            Route::get('/', [TenantController::class, 'Index'])->name('occupant.index');
            Route::get('/create', [TenantController::class, 'LoadCreateForm'])->name('occupant.create.form');
            Route::post('/create', [TenantController::class, 'create'])->name('occupant.store');
            Route::get('/single', [TenantController::class, 'loadUpdatePage'])->name('occupants.edit.view');
            Route::put('/update', [TenantController::class, 'update'])->name('occupant.update');
            Route::get('/image/{filename}', [TenantController::class, 'getImage'])->where('filename', '.*')->name('tenant.image');
            Route::delete('/{id}', [TenantController::class, 'destroy'])->name('occupant.destroy');
        });

        // Estate Owners routes
        Route::prefix('estate_owners')->group(function () {
            Route::get('/', [EstateOwnerController::class, 'index'])->name('estate_owners.index');
            Route::get('/create', [EstateOwnerController::class, 'create'])->name('estate_owners.create');
            Route::post('/', [EstateOwnerController::class, 'store'])->name('estate_owners.store');
            Route::get('/{id}', [EstateOwnerController::class, 'show'])->name('estate_owners.show');
            Route::get('/{id}/edit', [EstateOwnerController::class, 'edit'])->name('estate_owners.edit');
            Route::put('/{id}', [EstateOwnerController::class, 'update'])->name('estate_owners.update');
            Route::delete('/{id}', [EstateOwnerController::class, 'destroy'])->name('estate_owners.destroy');
        });

        // Property routes
        Route::prefix('property')->group(function () {
            Route::get('/', [PropertyController::class, 'blockIndex'])->name('property.index');
            Route::get('/store', [PropertyController::class, 'Create'])->name('property.create');
            Route::post('/store', [PropertyController::class, 'storeBlock'])->name('property.store');
            Route::get('/{id}', [PropertyController::class, 'showBlock'])->name('property.show');
            Route::put('/{id}/update', [PropertyController::class, 'blockUpdate'])->name('property.update');
            Route::delete('/{id}/delete', [PropertyController::class, 'blockDestroy'])->name('property.destroy');
            Route::get('/blocks/search', [PropertyController::class, 'search'])->name('property.search');
            Route::get('/blocks/import', [PropertyController::class, 'loadImport'])->name('property.import');
            Route::post('/blocks/import', [PropertyController::class, 'import'])->name('property.import.upload');
            Route::post('/lgvt', [PropertyController::class, 'getLgvt'])->name('get.lgvt');
        });

        // Shelter routes
        Route::prefix('shelter')->group(function () {
            Route::get('/', [ShelterController::class, 'index'])->name('shelters.index');
            Route::get('/create', [ShelterController::class, 'create'])->name('shelters.create');
            Route::post('/', [ShelterController::class, 'createShelter'])->name('shelters.store');
            Route::get('/{id}', [ShelterController::class, 'show'])->name('shelters.show');
            Route::get('/{id}/edit', [ShelterController::class, 'edit'])->name('shelters.edit');
            Route::put('/{id}', [ShelterController::class, 'update'])->name('shelters.update');
            Route::delete('/{id}', [ShelterController::class, 'destroy'])->name('shelters.destroy');
        });

        // Apartment routes
        Route::prefix('apartment')->group(function () {
            Route::get('/{block_id}/{shelter_id}', [ApartmentController::class, 'index'])->name('apartment.index');
            Route::post('/create', [ApartmentController::class, 'createOrUpdate'])->name('apartment.create');
            Route::delete('/{id}/delete', [ApartmentController::class, 'destroy'])->name('apartment.destroy');
        });

        // Accommodation routes
        Route::prefix('accommodation')->group(function () {
            Route::get('/', [AccommodationController::class, 'index'])->name('accommodation.index');
            Route::get('/block', [AccommodationController::class, 'accomBlock'])->name('accommodation.block');
            Route::post('/amenities/update', [ApartmentController::class, 'UpdateAmenitySize'])->name('amenity.size.update');
        });

        // Booking routes
        Route::prefix('accom')->group(function () {
            Route::post('/book', [BookingController::class, 'book'])->name('accommodation.book');
            Route::get('/book', [BookingController::class, 'getBooked'])->name('accommodation.booked');
            Route::delete('/{id}', [BookingController::class, 'cancelBooking'])->name('booked.cancel');
        });

        // Tenancy Type routes
        Route::prefix('Tenancy/type')->group(function () {
            Route::get('/', [TenancyTypeController::class, 'index'])->name('tenancy.index');
            Route::get('/create/tenancy', [TenancyTypeController::class, 'show'])->name('tenancy.show');
            Route::post('/create/tenancy', [TenancyTypeController::class, 'store'])->name('tenancy.store');
            Route::get('/edit/tenancy/{tenancyType}', [TenancyTypeController::class, 'edit'])->name('tenancy.edit');
            Route::put('/tenancy/update/{tenancyType}', [TenancyTypeController::class, 'update'])->name('tenancy.update');
            Route::delete('/tenancy/{tenancyType}', [TenancyTypeController::class, 'destroy'])->name('tenancy.destroy');
        });

        // Maintenance routes
        Route::prefix('maintenance')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('maintenance.index');
            Route::get('/create', [MaintenanceController::class, 'loadCreate'])->name('maintenance.create');
            Route::post('/create', [MaintenanceController::class, 'store'])->name('maintenance.store');
            Route::get('/import', [MaintenanceController::class, 'getImport'])->name('maintenance.getImport');
            Route::post('/import', [MaintenanceController::class, 'import'])->name('maintenance.import');
            Route::post('/search', [MaintenanceController::class, 'Search'])->name('maintenance.search');
            Route::get('/repairs/edit/{repair_id}', [MaintenanceController::class, 'loadEdit'])->name('maintenance.edit');
            Route::put('/repairs/update/{repair_id}', [MaintenanceController::class, 'update'])->name('maintenance.update');
            Route::delete('/{repair_id}', [MaintenanceController::class, 'Destroy'])->name('maintenance.destroy');
        });

        // Void routes
        Route::prefix('void')->group(function () {
            Route::get('/', [VoidsController::class, 'index'])->name('void.index');
            Route::get('/create', [VoidsController::class, 'show'])->name('void.create');
            Route::post('/store', [VoidsController::class, 'store'])->name('void.store');
            Route::get('/load/import', [VoidsController::class, 'voidImport'])->name('void.import.load');
            Route::post('/store/import', [VoidsController::class, 'store'])->name('void.import.store');
            Route::get('/edit/{id}', [VoidsController::class, 'edit'])->name('voids.edit');
            Route::put('/{id}', [VoidsController::class, 'update'])->name('voids.update');
            Route::delete('/{id}', [VoidsController::class, 'destroy'])->name('voids.destroy');
        });

        // ASB routes
        Route::prefix('asb')->group(function () {
            Route::get('/index', [AsbController::class, 'index'])->name('asb.index');
            Route::get('/load/create', [AsbController::class, 'LoadCreate'])->name('asb.create');
            Route::post('/store/asb', [AsbController::class, 'store'])->name('asb.store');
            Route::get('/edit/{asb_id}', [AsbController::class, 'edit'])->name('asb.edit');
            Route::put('/update/{asb_id}', [AsbController::class, 'update'])->name('asb.update');
            Route::delete('/{asb_id}', [AsbController::class, 'destroy'])->name('asb.destroy');
        });

        // Pest Control routes
        Route::prefix('pest')->group(function () {
            Route::get('/', [PestController::class, 'index'])->name('pest_control.index');
            Route::get('/create', [PestController::class, 'create'])->name('pest_control.create');
            Route::post('/', [PestController::class, 'store'])->name('pest_control.store');
            Route::get('/{pest_id}', [PestController::class, 'show'])->name('pest_control.show');
            Route::get('/{pest_id}/edit', [PestController::class, 'edit'])->name('pest_control.edit');
            Route::put('/{pest_id}', [PestController::class, 'update'])->name('pest_control.update');
            Route::delete('/delete/{pest_id}', [PestController::class, 'destroy'])->name('pest_control.destroy');
        });

        // Fob management routes
        Route::prefix('fobs')->group(function () {
            Route::get('/', [FobController::class, 'index'])->name('fobs.index');
            Route::get('/create', [FobController::class, 'create'])->name('fobs.create');
            Route::post('/', [FobController::class, 'store'])->name('fobs.store');
            Route::get('/{fob}', [FobController::class, 'show'])->name('fobs.show');
            Route::get('/{fob}/edit', [FobController::class, 'edit'])->name('fobs.edit');
            Route::put('/{fob}', [FobController::class, 'update'])->name('fobs.update');
            Route::delete('/{fob}', [FobController::class, 'destroy'])->name('fobs.destroy');
        });

        // Park management routes
        Route::prefix('parks')->group(function () {
            // Park Permits
            Route::get('/park/permits', [ParkController::class, 'indexPermits'])->name('park.permits.index');
            Route::get('/park/permits/create', [ParkController::class, 'createPermit'])->name('park.permits.create');
            Route::post('/park/permits', [ParkController::class, 'storePermit'])->name('park.permits.store');
            Route::get('/park/permits/{id}', [ParkController::class, 'editPermit'])->name('park.permits.edit');
            Route::put('/park/permits/{id}', [ParkController::class, 'updatePermit'])->name('park.permits.update');
            Route::delete('/park/permits/{id}', [ParkController::class, 'destroyPermit'])->name('park.permits.destroy');

            // Parks
            Route::get('/parks', [ParkController::class, 'indexParks'])->name('park.models.index');
            Route::get('/parks/create', [ParkController::class, 'createPark'])->name('park.models.create');
            Route::post('/parks', [ParkController::class, 'storePark'])->name('park.models.store');
            Route::get('/park/edit/{park_id}', [ParkController::class, 'editPark'])->name('park.models.edit');
            Route::put('/parks/{park_id}', [ParkController::class, 'updatePark'])->name('park.models.update');
            Route::delete('/parks/{park_id}', [ParkController::class, 'destroyPark'])->name('park.models.destroy');

            // Park Categories
            Route::get('/park/categories', [ParkController::class, 'indexCategories'])->name('park.categories.index');
            Route::post('/parking/inbound', [ParkController::class, 'inbound'])->name('parking.inbound');
            Route::post('/parking/outbound', [ParkController::class, 'outbound'])->name('parking.outbound');
             Route::get('/parking/capture', [ParkController::class, 'capture'])->name('parking.bound');
            Route::get('/parking/status', [ParkController::class, 'load_status'])->name('parking.load.status');
             Route::post('/parking/status', [ParkController::class, 'status'])->name('parking.status');
            Route::get('/park/categories/create', [ParkController::class, 'createCategory'])->name('park.categories.create');
            Route::post('/park/categories', [ParkController::class, 'storeCategory'])->name('park.categories.store');
            Route::get('/park/categories/edit/{id}', [ParkController::class, 'editCategory'])->name('park.categories.edit');
            Route::put('/park/categories/{id}', [ParkController::class, 'updateCategory'])->name('park.categories.update');
            Route::delete('/park/categories/{id}', [ParkController::class, 'destroyCategory'])->name('park.categories.destroy');

            // Park Taxes
            Route::get('/park/taxes', [ParkController::class, 'indexTaxes'])->name('park.taxes.index');
            Route::get('/park/taxes/create', [ParkController::class, 'createTax'])->name('park.taxes.create');
            Route::post('/park/taxes', [ParkController::class, 'storeTax'])->name('park.taxes.store');
            Route::get('/park/taxes/edit', [ParkController::class, 'editTax'])->name('park.taxes.edit');
            Route::put('/park/taxes/{tax_id}', [ParkController::class, 'updateTax'])->name('park.taxes.update');
            Route::delete('/park/taxes/{tax_id}', [ParkController::class, 'destroyTax'])->name('park.taxes.destroy');
        });

        // Rent routes
        Route::prefix('rents')->group(function () {
            Route::get('/rent/apartments/view', [RentController::class, 'ViewApartments'])->name('apartments.view');
            Route::get('/rent/account/create', [RentController::class, 'CreateRentAccount'])->name('rent.account');
            Route::post('/rent/account/create', [RentController::class, 'store'])->name('rent.store');
            Route::get('/accounts/active', [RentController::class, 'index'])->name('rent.active');
            Route::get('/rent/accounts/inactive', [RentController::class, 'inactive'])->name('rent.inactive');
            Route::get('/rent/accounts/history/{account_id}', [RentController::class, 'rentHistory'])->name('rent_accounts.transactions');
            Route::post('/rent/account/deactivate/{account_id}', [RentController::class, 'deactivate'])->name('rent_accounts.deactivate');
            Route::get('/rent/accounts/inactive/history/{account_id}', [RentController::class, 'inactiveHistory'])->name('rent_accounts_inactive.transactions');
            Route::get('/rent/accounts/cycle', [RentController::class, 'createCycle'])->name('rent.cycle');
            Route::post('/rent/accounts/renew', [RentController::class, 'Renew'])->name('rent.renew');
        });

        // Report routes
        Route::prefix('report')->group(function () {
            Route::get('/rent/generate', [ReportsController::class, 'Rent'])->name('rent.report');
            Route::post('/rent/generate', [ReportsController::class, 'RentReport'])->name('rent.report.generate');
            Route::get('/pest/control/report', [ReportsController::class, 'PestControl'])->name('pest_control.report');
            Route::post('/pest/control/report/generate', [ReportsController::class, 'PestControlReport'])->name('pest_control.report.generate');
            Route::get('/maintenance/report', [ReportsController::class, 'Maintenance'])->name('maintenance.report');
            Route::post('/maintenance/report/generate', [ReportsController::class, 'MaintenanceReport'])->name('maintenance.report.generate');
            Route::get('/voids/report', [ReportsController::class, 'Voids'])->name('voids.report');
            Route::post('/voids/report/generate', [ReportsController::class, 'VoidsReport'])->name('voids.report.generate');
             Route::get('/complaints', [ReportsController::class, 'complaints'])->name('complaints.report');
            Route::post('/complaints/generate', [ReportsController::class, 'ComplaintsReport'])->name('complaints.report.generate');
        });

        // Notification routes
        Route::prefix('notification')->group(function () {
        Route::get('/due/repairs', [NotificationController::class, 'checkDueRepairs'])->name('due.repairs');
        });

        // Location resource routes
        Route::resource('locations', Location::class);

        // Access management routes
        Route::prefix('access')->group(function () {
            // User CRUD routes
            Route::get('/users', [UserManagerController::class, 'index'])->name('access.users.index');
            Route::get('/users/create', [UserManagerController::class, 'create'])->name('access.users.create');
            Route::post('/users', [UserManagerController::class, 'store'])->name('access.users.store');
            Route::get('/users/{user}', [UserManagerController::class, 'show'])->name('access.users.show');
            Route::get('/users/{user}/edit', [UserManagerController::class, 'edit'])->name('access.users.edit');
            Route::put('/users/{user}', [UserManagerController::class, 'update'])->name('access.users.update');
            Route::delete('/users/{user}', [UserManagerController::class, 'destroy'])->name('access.users.destroy');

            // Role CRUD routes
            Route::get('/roles', [RolesController::class, 'index'])->name('access.roles.index');
            Route::get('/roles/create', [RolesController::class, 'create'])->name('access.roles.create');
            Route::post('/roles', [RolesController::class, 'store'])->name('access.roles.store');
            Route::get('/roles/{role}', [RolesController::class, 'show'])->name('access.roles.show');
            Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])->name('access.roles.edit');
            Route::put('/roles/{role}', [RolesController::class, 'update'])->name('access.roles.update');
            Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->name('access.roles.destroy');

            // Assign role(s) to user routes
            Route::get('/users/{user}/roles', [RolesController::class, 'editUserRoles'])->name('access.users.roles.edit');
            Route::post('/users/{user}/roles', [RolesController::class, 'updateUserRoles'])->name('access.users.roles.update');
        });
        
         Route::prefix('complaints')->group(function () {
            Route::get('/create', [ComplaintController::class, 'create'])->name('complaints.create');
             Route::get('/home', [ComplaintController::class, 'index'])->name('complaints.index');
             Route::get('/edit/{id}', [ComplaintController::class, 'edit'])->name('complaints.edit');
             Route::delete('/delete/{id}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');
            Route::post('/create', [ComplaintController::class, 'store'])->name('complaints.store');
            Route::put('update/{id}', [ComplaintController::class, 'update'])->name('complaints.update');
            
        });
     

    // =========================
    // Subscription Plans
    // =========================
    Route::get('/subscriptions/plans', [SubscriptionPlanController::class, 'index'])
        ->name('subscriptions.index');

    Route::get('/subscriptions/plans/create', [SubscriptionPlanController::class, 'create'])
        ->name('subscriptions.create');

    Route::post('/subscriptions/plans', [SubscriptionPlanController::class, 'store'])
        ->name('subscriptions.store');

    Route::get('/subscriptions/plans/{id}', [SubscriptionPlanController::class, 'show'])
        ->name('subscriptions.show');

    Route::get('/subscriptions/plans/{id}/edit', [SubscriptionPlanController::class, 'edit'])
        ->name('subscriptions.edit');

    Route::put('/subscriptions/plans/{id}', [SubscriptionPlanController::class, 'update'])
        ->name('subscriptions.update');

    Route::delete('/subscriptions/plans/{id}', [SubscriptionPlanController::class, 'destroy'])
        ->name('subscriptions.destroy');


    // =========================
    // Subscription Accounts
    // =========================
    Route::get('/subscription/accounts', [SubscriptionAccountController::class, 'index'])
        ->name('subscription.account.index');

    Route::get('/subscription/accounts/create', [SubscriptionAccountController::class, 'create'])
        ->name('subscription.account.create');

    Route::post('/subscription/accounts', [SubscriptionAccountController::class, 'store'])
        ->name('subscription.account.store');

    Route::get('/subscription/accounts/{id}', [SubscriptionAccountController::class, 'show'])
        ->name('subscription.account.show');

    Route::get('/subscription/accounts/{id}/edit', [SubscriptionAccountController::class, 'edit'])
        ->name('subscription.account.edit');

    Route::put('/subscription/accounts/{id}', [SubscriptionAccountController::class, 'update'])
        ->name('subscription.account.update');

    Route::delete('/subscription/accounts/{id}', [SubscriptionAccountController::class, 'destroy'])
        ->name('subscription.account.destroy');
        Route::get('/brand_details', [BrandController::class, 'index'])->name('brand.index');
        Route::get('/brand_details/create', [BrandController::class, 'create'])->name('brand.create');
        Route::post('/brand_details', [BrandController::class, 'store'])->name('brand.store');
        Route::get('/brand_details/{id}', [BrandController::class, 'show'])->name('brand.show');
        Route::get('/brand_details/{id}/edit', [BrandController::class, 'edit'])->name('brand.edit');
        Route::put('/brand_details/{id}', [BrandController::class, 'update'])->name('brand.update');
        Route::delete('/brand_details/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');


    });
});
});