<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\POSController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsAdminLoggedIn;
use App\Http\Middleware\isCashierLoggedIn;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Offset;

Route::get('/', function () {
    return view('receipt');
})->name('welcome');
Route::post('/login', [AuthController::class, 'authLoginCashier'])->name('auth_login');

Route::middleware([isCashierLoggedIn::class])->group(function(){
    Route::get('/add', [AuthController::class, 'addCashier'])->name('auth_register');
    // Route::get('/dashboard', [POSController::class, 'dashboard'])->middleware(AuthenticateCashier::class)->name('dashboard');
    Route::get('/dashboard', [POSController::class, 'dashboard'])->name('dashboard');
    Route::get('/ticket_details', [POSController::class, 'ticketDetails'])->name('ticket_details');
    Route::get('/history', [POSController::class, 'history'])->name('history');
    Route::post('/sale', [POSController::class, 'sale'])->name('sale');
    Route::get('/purchased_date', [POSController::class, 'purchasedDate'])->name('purchased_date');
    Route::get('/cashier', [POSController::class, 'cashier'])->name('cashier');
    Route::post('/cashier', [POSController::class, 'startShift'])->name('start_shift');
    Route::get('/history_ticket/{ticket}', [POSController::class, 'historyTicket'])->name('history.ticket');
    Route::post('/end-shift', [AuthController::class, 'endShift'])->name('end-shift');
    Route::post('/cash-management', [POSController::class, 'cashManagement'])->name('cash_management');
    Route::get('/inventory', [POSController::class, 'inventory'])->name('inventory');
    Route::get('/orders', [POSController::class, 'orders'])->name('orders');
    Route::get('/livesearch/{key?}', [POSController::class, 'livesearch'])->name('livesearch');
    Route::get('/item-search/{key?}', [POSController::class, 'itemSearch'])->name('item_search');
    Route::get('/supplier-name/{name}', [POSController::class, 'ordersFromSupplier'])->name('supplier_name');
    Route::post('/complete-order', [POSController::class, 'completeOrder'])->name('complete_order');
});

Route::get('/back-office/login', [OfficeController::class, 'adminLogin'])->name('office.login');
Route::post('/back-office/auth/login', [AuthController::class, 'authLoginAdmin'])->name('office.login.post');
Route::get('add_admin', [AuthController::class, 'addAdmin']);

Route::middleware([IsAdminLoggedIn::class])->group(function(){
    Route::get('/back-office/dashboard', [OfficeController::class, 'dashboard'])->name('office.dashboard');
    Route::get('/back-office/inventory', [OfficeController::class, 'inventory'])->name('office.inventory');
    Route::get('back-office/logout', [AuthController::class, 'adminLogout'])->name('office.logout');
    Route::get('/back-office/purchased_date', [OfficeController::class, 'purchasedDate'])->name('office.purchased_date');
    Route::get('/back-office/stocks_adjustments', [OfficeController::class, 'stocksAdjustment'])->name('office.stocks_adjustment');
    Route::get('/back-office/items_list', [OfficeController::class, 'itemsList'])->name('office.items_list');
    Route::get('/back-office/create_item', [OfficeController::class, 'createItem'])->name('office.create_item');
    Route::post('/back-office/add_item', [OfficeController::class, 'addItem'])->name('office.add_item');
    Route::get('/back-office/view_item/{sku}', [OfficeController::class, 'viewItem'])->name('office.view_item');
    Route::POST('/back-office/update_item', [OfficeController::class, 'updateItem'])->name('office.update_item');
    Route::get('/back-office/update_stocks', [OfficeController::class, 'updateStocks'])->name('office.update_stocks');
    Route::get('/back-office/sales_by_item', [OfficeController::class, 'salesByItem'])->name('office.sales_by_item');
    Route::GET('/back-office/get_monthly_sales', [OfficeController::class, 'getMonthlySales'])->name('office.get_monthly_sales');
    Route::get('/back-office/sales_history', [OfficeController::class, 'salesHistory'])->name('office.sales_history');
    Route::get('/back-office/history_ticket/{ticket}', [OfficeController::class, 'historyTicket'])->name('office.history_ticket');
    Route::get('/back-office/sales_by_item/{item_name}', [OfficeController::class, 'itemName'])->name('office.item_name');
    Route::get('/back-office/qr', [OfficeController::class, 'qrPrinting'])->name('qr_printing');
    Route::get('/back-office/qr-to-print', [OfficeController::class, 'qrToPrint'])->name('qr_for_printing');
    Route::get('/back-office/stocks_adjustments/{item_name}', [OfficeController::class, 'adjustStocks'])->name('office.adjust');
    Route::get('/back-office/cashiers', [OfficeController::class, 'cashiers'])->name('office.cashiers');
    Route::post('/back-office/add_cashier', [AuthController::class, 'addCashier'])->name('office.add_cashier');
    Route::get('/back-office/supplier', [OfficeController::class, 'suppliers'])->name('office.supplier');
    Route::post('/back-office/add-supplier', [OfficeController::class, 'addSuppliers'])->name('office.add_supplier');
    Route::get('/back-office/ordering', [OfficeController::class, 'ordering'])->name('office.ordering');
    Route::get('/back-office/item_search/{key?}', [OfficeController::class, 'itemSearch'])->name('office.item_search');
    Route::get('/back-office/supplier_search/{key?}', [OfficeController::class, 'supplierSearch'])->name('office.supplier_search');
    Route::POST('/back-office/place-order', [OfficeController::class, 'placeOrder'])->name('office.place_order');
});


