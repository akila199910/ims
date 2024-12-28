<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Business Routes
|--------------------------------------------------------------------------
|
| This Route related to business
|
*/

Route::middleware(['auth', 'isUserExist', 'isCompanyId'])->group(function () {

    //Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Business\DashboardController::class, 'index'])->name('business.dashboard');
    Route::get('/dashboard/graph', [App\Http\Controllers\Business\DashboardController::class, 'graph'])->name('dashboard.graph');
    Route::post('/dashboard/get_purchase', [App\Http\Controllers\Business\DashboardController::class, 'get_purchase'])->name('dashboard.get_purchase');
    Route::get('/dashboard/get_purchase_list', [App\Http\Controllers\Business\DashboardController::class, 'get_purchase_list'])->name('dashboard.get_purchase_list');

    // Stock Transfer view
    Route::post('/dashboard/get_stockTransfer', [App\Http\Controllers\Business\DashboardController::class, 'get_stockTransfer'])->name('dashboard.get_stockTransfer');
    Route::get('/dashboard/get_stockTransfer_list', [App\Http\Controllers\Business\DashboardController::class, 'get_stockTransfer_list'])->name('dashboard.get_stockTransfer_list');

    //category
    Route::get('/category', [App\Http\Controllers\Business\CategoryController::class, 'index'])->name('business.category');
    Route::get('/category/create', [App\Http\Controllers\Business\CategoryController::class, 'create_form'])->name('business.category.create.form');
    Route::post('/category/create', [App\Http\Controllers\Business\CategoryController::class, 'create'])->name('business.category.create');
    Route::get('/category/update/{id}', [App\Http\Controllers\Business\CategoryController::class, 'update_form'])->name('business.category.update.form');
    Route::post('/category/update', [App\Http\Controllers\Business\CategoryController::class, 'update'])->name('business.category.update');
    Route::post('/category/delete', [App\Http\Controllers\Business\CategoryController::class, 'delete'])->name('business.category.delete');
    Route::get('/category/view/{ref_no}', [App\Http\Controllers\Business\CategoryController::class, 'view_details'])->name('business.category.view_details');


    //Sub category
    Route::get('/sub_category', [App\Http\Controllers\Business\SubCategoryController::class, 'index'])->name('business.sub_category');
    Route::get('/sub_category/create', [App\Http\Controllers\Business\SubCategoryController::class, 'create_form'])->name('business.sub_category.create.form');
    Route::post('/sub_category/create', [App\Http\Controllers\Business\SubCategoryController::class, 'create'])->name('business.sub_category.create');
    Route::get('/sub_category/update/{id}', [App\Http\Controllers\Business\SubCategoryController::class, 'update_form'])->name('business.sub_category.update.form');
    Route::post('/sub_category/update', [App\Http\Controllers\Business\SubCategoryController::class, 'update'])->name('business.sub_category.update');
    Route::post('/sub_category/delete', [App\Http\Controllers\Business\SubCategoryController::class, 'delete'])->name('business.sub_category.delete');
    Route::get('/sub_category/view/{ref_no}', [App\Http\Controllers\Business\SubCategoryController::class, 'view_details'])->name('business.sub_category.view_details');



    //Units
    Route::get('/units', [App\Http\Controllers\Business\UnitsController::class, 'index'])->name('business.units');
    Route::get('/units/create', [App\Http\Controllers\Business\UnitsController::class, 'create_form'])->name('business.units.create.form');
    Route::post('/units/create', [App\Http\Controllers\Business\UnitsController::class, 'create'])->name('business.units.create');
    Route::get('/units/update/{id}', [App\Http\Controllers\Business\UnitsController::class, 'update_form'])->name('business.units.update.form');
    Route::post('/units/update', [App\Http\Controllers\Business\UnitsController::class, 'update'])->name('business.units.update');
    Route::post('/units/delete', [App\Http\Controllers\Business\UnitsController::class, 'delete'])->name('business.units.delete');
    Route::get('/units/view/{ref_no}', [App\Http\Controllers\Business\UnitsController::class, 'view_details'])->name('business.units.view_details');


    // Suppliers
    Route::get('/vendors', [App\Http\Controllers\Business\SupplierController::class, 'index'])->name('business.suppliers');
    Route::get('/vendors/create', [App\Http\Controllers\Business\SupplierController::class, 'create_form'])->name('business.suppliers.create.form');
    Route::post('/vendors/add_update_contacts', [App\Http\Controllers\Business\SupplierController::class, 'add_update_contacts'])->name('business.suppliers.add_update_contacts');
    Route::post('/vendors/create', [App\Http\Controllers\Business\SupplierController::class, 'create'])->name('business.suppliers.create');
    Route::get('/vendors/update/{id}', [App\Http\Controllers\Business\SupplierController::class, 'update_form'])->name('business.suppliers.update.form');
    Route::post('/vendors/update', [App\Http\Controllers\Business\SupplierController::class, 'update'])->name('business.suppliers.update');
    Route::post('/vendors/delete', [App\Http\Controllers\Business\SupplierController::class, 'delete'])->name('business.suppliers.delete');
    Route::post('/vendors/update_status', [App\Http\Controllers\Business\SupplierController::class, 'update_status'])->name('business.suppliers.update_status');
    Route::get('/vendors/view/{ref_no}', [App\Http\Controllers\Business\SupplierController::class, 'view_details'])->name('business.suppliers.view_details');


    // warehouse
    Route::get('/warehouse', [App\Http\Controllers\Business\WareHouseController::class, 'index'])->name('business.warehouse');
    Route::get('/warehouse/create', [App\Http\Controllers\Business\WareHouseController::class, 'create_form'])->name('business.warehouse.create.form');
    Route::post('/warehouse/create', [App\Http\Controllers\Business\WareHouseController::class, 'create'])->name('business.warehouse.create');
    Route::get('/warehouse/update/{id}', [App\Http\Controllers\Business\WareHouseController::class, 'update_form'])->name('business.warehouse.update.form');
    Route::post('/warehouse/update', [App\Http\Controllers\Business\WareHouseController::class, 'update'])->name('business.warehouse.update');
    Route::post('/warehouse/delete', [App\Http\Controllers\Business\WareHouseController::class, 'delete'])->name('business.warehouse.delete');
    Route::get('/warehouse/view/{id}', [App\Http\Controllers\Business\WareHouseController::class, 'view_form'])->name('business.warehouse.view.form');
    Route::get('/warehouse/get_products', [App\Http\Controllers\Business\WareHouseController::class, 'get_products'])->name('business.warehouse.get_products');
    Route::post('/warehouse/products_delete', [App\Http\Controllers\Business\WareHouseController::class, 'products_delete'])->name('business.warehouse.products.delete');
    Route::get('/warehouse/get_details_product', [App\Http\Controllers\Business\WareHouseController::class, 'get_details_product'])->name('business.warehouse.get_details_product');
    Route::post('/warehouse/update_product', [App\Http\Controllers\Business\WareHouseController::class, 'update_product'])->name('business.warehouse.update_product');
    Route::get('/warehouse/view/{ref_no}', [App\Http\Controllers\Business\WareHouseController::class, 'view_details'])->name('business.warehouse.view_details');


    // Inventory
    Route::get('/products', [App\Http\Controllers\Business\ProductsController::class, 'index'])->name('business.products');
    Route::get('/products/create', [App\Http\Controllers\Business\ProductsController::class, 'create_form'])->name('business.products.create.form');
    Route::get('/products/get_subcategory', [App\Http\Controllers\Business\ProductsController::class, 'get_subcategory'])->name('business.products.get_subcategory');
    Route::post('/products/create', [App\Http\Controllers\Business\ProductsController::class, 'create'])->name('business.products.create');
    Route::get('/products/update/{id}', [App\Http\Controllers\Business\ProductsController::class, 'update_form'])->name('business.products.update.form');
    Route::post('/products/update', [App\Http\Controllers\Business\ProductsController::class, 'update'])->name('business.products.update');
    Route::post('/products/delete', [App\Http\Controllers\Business\ProductsController::class, 'delete'])->name('business.products.delete');
    Route::get('/products/view/{id}', [App\Http\Controllers\Business\ProductsController::class, 'view_form'])->name('business.products.view.form');
    Route::get('/products/get_wareHouse', [App\Http\Controllers\Business\ProductsController::class, 'get_wareHouse'])->name('business.products.get_wareHouse');
    Route::get('/products/get_details', [App\Http\Controllers\Business\ProductsController::class, 'get_details'])->name('business.products.get_details');
    Route::get('/products/get_details/{id}', [App\Http\Controllers\Business\ProductsController::class, 'getDetails'])->name('business.products.getDetails');
    Route::post('/products/update_wareHouse', [App\Http\Controllers\Business\ProductsController::class, 'update_wareHouse'])->name('business.products.update_wareHouse');

    //User Management
    Route::get('/users', [App\Http\Controllers\Business\UserManageController::class, 'index'])->name('business.users');
    Route::get('/users/create', [App\Http\Controllers\Business\UserManageController::class, 'create_form'])->name('business.users.create.form');
    Route::post('/users/create', [App\Http\Controllers\Business\UserManageController::class, 'create'])->name('business.users.create');
    Route::get('/users/update/{ref_no}', [App\Http\Controllers\Business\UserManageController::class, 'update_form'])->name('business.users.update.form');
    Route::post('/users/update', [App\Http\Controllers\Business\UserManageController::class, 'update'])->name('business.users.update');
    Route::post('/users/delete', [App\Http\Controllers\Business\UserManageController::class, 'delete'])->name('business.users.delete');
    Route::get('/users/view/{ref_no}', [App\Http\Controllers\Business\UserManageController::class, 'view_details'])->name('business.users.view_details');


    // Purchase Order
    Route::get('/purchaseorder', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'index'])->name('business.purchaseorder');
    Route::get('/purchaseorder/create', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'create_form'])->name('business.purchaseorder.create.form');
    Route::get('/purchaseorder/get_products', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'get_products'])->name('business.purchaseorder.get_products');
    Route::post('/purchaseorder/purchase_item_validation', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'purchase_item_validation'])->name('business.purchaseorder.purchase_item_validation');
    Route::get('/purchaseorder/get_subcategory', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'get_subcategory'])->name('business.purchaseorder.get_subcategory');
    Route::post('/purchaseorder/create', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'create'])->name('business.purchaseorder.create');
    Route::get('/purchaseorder/update/{id}', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'update_form'])->name('business.purchaseorder.update.form');
    Route::post('/purchaseorder/update', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'update'])->name('business.purchaseorder.update');
    Route::post('/purchaseorder/delete', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'delete'])->name('business.purchaseorder.delete');
    Route::get('/purchaseorder/get_order_items', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'get_order_items'])->name('business.purchaseorder.get_order_items.form');
    Route::get('/purchaseorder/get_order_items/list', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'get_order_items_list'])->name('business.purchaseorder.get_order_items.list');
    Route::get('/purchaseorder/get_order_items/list_view', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'get_order_items_list_view'])->name('business.purchaseorder.get_order_items.list_view');
    Route::post('/purchaseorder/delete_item', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'delete_item'])->name('business.purchaseorder.delete_item');
    Route::post('/purchaseorder/add_purchase_item', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'add_purchase_item'])->name('business.purchaseorder.add_purchase_item');
    Route::post('/purchaseorder/send_mail', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'send_mail'])->name('business.purchaseorder.send_mail');
    Route::post('/purchaseorder/product_subtotal', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'product_subtotal'])->name('business.purchaseorder.product_subtotal');
    Route::post('/purchaseorder/get_item_info', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'get_item_info'])->name('business.purchaseorder.get_item_info');
    Route::post('/purchaseorder/update_qty', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'update_qty'])->name('business.purchaseorder.update_qty');
    Route::get('/purchaseorder/detail_view/{id}', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'detail_view'])->name('business.purchaseorder.detail.view');
    Route::post('/purchaseorder/update_status', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'update_status'])->name('business.purchaseorder.update_status');
    Route::get('/purchaseorder/download_pdf/{id}', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'download_pdf'])->name('business.purchaseorder.download_pdf');
    Route::get('/purchaseorder/order_receive/{id}', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'order_receive'])->name('business.purchaseorder.order.receive');
    Route::get('/purchaseorder/approval_history/{id}', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'approval_histories'])->name('business.purchaseorder.approval_histories');
    Route::get('/purchaseorder/approval_history_list', [App\Http\Controllers\Business\PurchaseOrdersController::class, 'approval_history_list'])->name('business.purchaseorder.approval_history_list');

    // Payment History
    Route::get('/purchaseorder/payments/{id}', [App\Http\Controllers\Business\PurchasePaymentController::class, 'index'])->name('business.purchaseorder.payments');
    Route::get('/purchaseorder/payments_list', [App\Http\Controllers\Business\PurchasePaymentController::class, 'payments_list'])->name('business.purchaseorder.payments.list');
    Route::post('/purchaseorder/payment/load_paid_due', [App\Http\Controllers\Business\PurchasePaymentController::class, 'load_paid_due'])->name('business.purchaseorder.payments.load_paid_due');
    Route::get('/purchaseorder/payment/create', [App\Http\Controllers\Business\PurchasePaymentController::class, 'create_form'])->name('business.purchaseorder.payments.create.form');
    Route::post('/purchaseorder/payment/create', [App\Http\Controllers\Business\PurchasePaymentController::class, 'create'])->name('business.purchaseorder.payments.create');
    Route::get('/purchaseorder/payment/update', [App\Http\Controllers\Business\PurchasePaymentController::class, 'update_form'])->name('business.purchaseorder.payments.update.form');
    Route::post('/purchaseorder/payment/update', [App\Http\Controllers\Business\PurchasePaymentController::class, 'update'])->name('business.purchaseorder.payments.update');
    Route::post('/purchaseorder/payment/delete', [App\Http\Controllers\Business\PurchasePaymentController::class, 'delete'])->name('business.purchaseorder.payments.delete');

    // Purchase List
    Route::get('/purchases', [App\Http\Controllers\Business\PurchaseController::class, 'index'])->name('business.purchases');
    Route::get('/purchases/view/{ref_no}', [App\Http\Controllers\Business\PurchaseController::class, 'view_details'])->name('business.purchase.view_details');
    Route::get('/purchases/item_list', [App\Http\Controllers\Business\PurchaseController::class, 'item_list'])->name('business.purchases.item_list');
    Route::post('/purchases/re_order', [App\Http\Controllers\Business\PurchaseController::class, 're_order'])->name('business.purchases.re_order');
    Route::get('/purchases/payments/{id}', [App\Http\Controllers\Business\PurchaseController::class, 'payments'])->name('business.purchases.payments');
    Route::get('/purchases/get_payments', [App\Http\Controllers\Business\PurchaseController::class, 'get_payments'])->name('purchases.get_payments');
    Route::get('/purchases/update_form', [App\Http\Controllers\Business\PurchaseController::class, 'update_form'])->name('business.purchases.update.form');
    Route::post('/purchases/delete', [App\Http\Controllers\Business\PurchaseController::class, 'delete'])->name('business.purchases.delete');
    Route::post('/purchases/update', [App\Http\Controllers\Business\PurchaseController::class, 'update'])->name('business.purchases.update');
    Route::post('/purchases/store_payments', [App\Http\Controllers\Business\PurchaseController::class, 'store_payments'])->name('purchases.store_payments');
    Route::post('/purchases/deletePayement', [App\Http\Controllers\Business\PurchaseController::class, 'deletePayement'])->name('purchases.deletePayement');
    Route::get('/purchases/approval_history/{id}', [App\Http\Controllers\Business\PurchaseController::class, 'approval_histories'])->name('purchases.approval_histories');


    //Stock Adjusted
    Route::get('/stock_adjusted', [App\Http\Controllers\Business\StockAdjustedController::class, 'index'])->name('business.stock_adjusted');
    Route::get('/stock_adjusted/create', [App\Http\Controllers\Business\StockAdjustedController::class, 'create_form'])->name('business.stock_adjusted.create.form');
    Route::post('/stock_adjusted/create', [App\Http\Controllers\Business\StockAdjustedController::class, 'create'])->name('business.stock_adjusted.create');
    Route::get('/stock_adjusted/update/{id}', [App\Http\Controllers\Business\StockAdjustedController::class, 'update_form'])->name('business.stock_adjusted.update.form');
    Route::post('/stock_adjusted/update', [App\Http\Controllers\Business\StockAdjustedController::class, 'update'])->name('business.stock_adjusted.update');
    Route::post('/stock_adjusted/delete', [App\Http\Controllers\Business\StockAdjustedController::class, 'delete'])->name('business.stock_adjusted.delete');
    Route::post('/stock_adjusted/Stock_item_filter', [App\Http\Controllers\Business\StockAdjustedController::class, 'Stock_item_filter'])->name('stock_adjusted.Stock_item_filter');
    Route::post('/stock_adjusted/get_ware_house', [App\Http\Controllers\Business\StockAdjustedController::class, 'get_ware_house'])->name('stock_adjusted.get_ware_house');
    Route::post('/stock_adjusted/add_update_item', [App\Http\Controllers\Business\StockAdjustedController::class, 'add_update_item'])->name('stock_adjusted.add_update_item');
    Route::get('/stock_adjusted/product_list', [App\Http\Controllers\Business\StockAdjustedController::class, 'product_list'])->name('business.stock_adjusted.product_list');
    Route::get('/stock_adjusted/get_item_list', [App\Http\Controllers\Business\StockAdjustedController::class, 'get_item_list'])->name('business.stock_adjusted.get_item_list');
    Route::post('/stock_adjusted/add_item', [App\Http\Controllers\Business\StockAdjustedController::class, 'add_item'])->name('business.stock_adjusted.add_item');
    Route::post('/stock_adjusted/delete_item', [App\Http\Controllers\Business\StockAdjustedController::class, 'delete_item'])->name('business.stock_adjusted.delete_item');
    Route::get('/stock_adjusted/view/{ref_no}', [App\Http\Controllers\Business\StockAdjustedController::class, 'view_details'])->name('business.stock_adjusted.view_details');


    //stockShare
    Route::get('/stock_transfer', [App\Http\Controllers\Business\StockTransferController::class, 'index'])->name('business.stock_transfer');
    Route::get('/stock_transfer/create', [App\Http\Controllers\Business\StockTransferController::class, 'create_form'])->name('business.stock_transfer.create.form');
    Route::get('/stock_transfer/get_warehouse', [App\Http\Controllers\Business\StockTransferController::class, 'get_warehouse'])->name('business.stock_transfer.get_warehouse');
    Route::get('/stock_transfer/get_trasnfer_item', [App\Http\Controllers\Business\StockTransferController::class, 'get_trasnfer_item'])->name('business.stock_transfer.get_trasnfer_item');
    Route::post('/stock_transfer/add_update_item', [App\Http\Controllers\Business\StockTransferController::class, 'add_update_item'])->name('business.stock_transfer.add_update_item');
    Route::post('/stock_transfer/get_product', [App\Http\Controllers\Business\StockTransferController::class, 'get_product'])->name('business.stock_transfer.get_product');
    Route::post('/stock_transfer/create', [App\Http\Controllers\Business\StockTransferController::class, 'create'])->name('business.stock_transfer.create');
    Route::get('/stock_transfer/update_form', [App\Http\Controllers\Business\StockTransferController::class, 'update_form'])->name('business.stock_transfer.update.form');
    Route::post('/stock_transfer/update', [App\Http\Controllers\Business\StockTransferController::class, 'update'])->name('business.stock_transfer.update');
    Route::post('/stock_transfer/delete', [App\Http\Controllers\Business\StockTransferController::class, 'delete'])->name('business.stock_transfer.delete');
    Route::get('/stock_transfer/view/{ref_no}', [App\Http\Controllers\Business\StockTransferController::class, 'view_details'])->name('business.stock_transfer.view_details');


    //Purchase Return
    Route::get('/purchase_return', [App\Http\Controllers\Business\PurchaseReturnController::class, 'index'])->name('business.purchase_return');
    Route::post('/purchase_return/pur_item_filter', [App\Http\Controllers\Business\PurchaseReturnController::class, 'pur_item_filter'])->name('purchase_return.pur_item_filter');
    Route::get('/purchase_return/create', [App\Http\Controllers\Business\PurchaseReturnController::class, 'create_form'])->name('business.purchase_return.create.form');
    Route::post('/purchase_return/create', [App\Http\Controllers\Business\PurchaseReturnController::class, 'create'])->name('business.purchase_return.create');
    Route::post('/purchase_return/get_product', [App\Http\Controllers\Business\PurchaseReturnController::class, 'get_product'])->name('business.purchase_return.get_product');
    Route::post('/purchase_return/purchase_item_validation', [App\Http\Controllers\Business\PurchaseReturnController::class, 'purchase_item_validation'])->name('business.purchase_return.purchase_item_validation');
    Route::post('/purchase_return/update_status', [App\Http\Controllers\Business\PurchaseReturnController::class, 'update_status'])->name('business.purchase_return.update_status');
    Route::get('/purchase_return/detail_view/{id}', [App\Http\Controllers\Business\PurchaseReturnController::class, 'detail_view'])->name('business.purchase_return.detail.view');
    Route::get('/purchase_return/update/{id}', [App\Http\Controllers\Business\PurchaseReturnController::class, 'update_form'])->name('business.purchase_return.update.form');
    Route::post('/purchase_return/update', [App\Http\Controllers\Business\PurchaseReturnController::class, 'update'])->name('business.purchase_return.update');
    Route::post('/purchase_return/delete', [App\Http\Controllers\Business\PurchaseReturnController::class, 'delete'])->name('business.purchase_return.delete');
    Route::get('/purchase_return/get_order_items', [App\Http\Controllers\Business\PurchaseReturnController::class, 'get_order_items'])->name('business.purchase_return.get_order_items.form');
    Route::get('/purchase_return/get_item_list', [App\Http\Controllers\Business\PurchaseReturnController::class, 'get_item_list'])->name('business.purchase_return.get_item_list');
    Route::get('/purchase_return/item_list', [App\Http\Controllers\Business\PurchaseReturnController::class, 'item_list'])->name('business.purchase_return.item_list');
    Route::post('/purchase_return/add_item', [App\Http\Controllers\Business\PurchaseReturnController::class, 'add_item'])->name('business.purchase_return.add_item');
    Route::post('/purchase_return/delete_item', [App\Http\Controllers\Business\PurchaseReturnController::class, 'delete_item'])->name('business.purchase_return.delete_item');
    Route::get('/purchase_return/get_order_items/list', [App\Http\Controllers\Business\PurchaseReturnController::class, 'get_return_items_list'])->name('business.purchase_return.get_return_items.list');
    Route::get('/purchase_return/available_products', [App\Http\Controllers\Business\PurchaseReturnController::class, 'available_products'])->name('business.purchase_return.available.products');
    Route::post('/purchase_return/product_subtotal', [App\Http\Controllers\Business\PurchaseReturnController::class, 'product_subtotal'])->name('business.purchase_return.product_subtotal');
    Route::get('/purchase_return/approval_history/{id}', [App\Http\Controllers\Business\PurchaseReturnController::class, 'approval_histories'])->name('business.purchase_return.approval_histories');
    Route::get('/purchase_return/approval_history_list', [App\Http\Controllers\Business\PurchaseReturnController::class, 'approval_history_list'])->name('business.purchase_return.approval_history_list');
    Route::get('/purchase_return/download_pdf/{id}', [App\Http\Controllers\Business\PurchaseReturnController::class, 'download_pdf'])->name('business.purchase_return.download_pdf');

    // Route::get('/purchase_return/view/{ref_no}', [App\Http\Controllers\Business\PurchaseReturnController::class, 'view_details'])->name('business.purchase_return.view_details');
    // Route::get('/purchase_return/get_products', [App\Http\Controllers\Business\PurchaseReturnController::class, 'get_products'])->name('business.purchase_return.get_products');
    // Route::post('/purchase_return/product_subtotal', [App\Http\Controllers\Business\PurchaseReturnController::class, 'product_subtotal'])->name('business.purchase_return.product_subtotal');
    // Route::post('/purchase_return/add_update_item', [App\Http\Controllers\Business\PurchaseReturnController::class, 'add_update_item'])->name('purchase_return.add_update_item');


    // Write off
    Route::get('/writeoff', [App\Http\Controllers\Business\WriteoffController::class, 'index'])->name('business.writeoff');
    Route::get('/writeoff/create', [App\Http\Controllers\Business\WriteoffController::class, 'create_form'])->name('business.writeoff.create.form');
    Route::post('/writeoff/create', [App\Http\Controllers\Business\WriteoffController::class, 'create'])->name('business.writeoff.create');
    Route::get('/writeoff/update/{id}', [App\Http\Controllers\Business\WriteoffController::class, 'update_form'])->name('business.writeoff.update.form');
    Route::post('/writeoff/update', [App\Http\Controllers\Business\WriteoffController::class, 'update'])->name('business.writeoff.update');
    Route::post('/writeoff/delete', [App\Http\Controllers\Business\WriteoffController::class, 'delete'])->name('business.writeoff.delete');
    Route::post('/writeoff/item_filter', [App\Http\Controllers\Business\WriteoffController::class, 'item_filter'])->name('business.writeoff.item_filter');
    Route::post('/writeoff/getdetails', [App\Http\Controllers\Business\WriteoffController::class, 'get_details'])->name('business.writeoff.get_details');
    Route::get('/writeoff/view/{ref_no}', [App\Http\Controllers\Business\WriteoffController::class, 'view_details'])->name('business.writeoff.view_details');


    //profile
    Route::get('/profile', [App\Http\Controllers\Business\ProfileController::class, 'profile'])->name('business.profile');
    Route::post('/profile_update', [App\Http\Controllers\Business\ProfileController::class, 'profileUpdate'])->name('business.profile_update');
    Route::post('/password_update', [App\Http\Controllers\Business\ProfileController::class, 'passwordUpdate'])->name('business.password_update');

    // Low Stock
    Route::get('/low_stock', [App\Http\Controllers\Business\LowStockController::class, 'index'])->name('business.low_stock');
    Route::post('/low_stock/low_stock_export', [App\Http\Controllers\Business\LowStockController::class, 'lowStock_export'])->name('business.low_stock.lowStock_export');

    // Reports purchase
    Route::get('/reports/purchase_report', [App\Http\Controllers\Business\ReportController::class, 'purchase_list'])->name('business.purchase_report');
    Route::post('/reports/purchase_report/export', [App\Http\Controllers\Business\ReportController::class, 'purchase_export'])->name('business.purchase_report.export');

    // Reports StockTransfer
    Route::get('/reports/stocktransfer_rep/list', [App\Http\Controllers\Business\ReportController::class, 'stockTransfer_list'])->name('business.stockTransfer_rep');
    Route::post('/reports/stocktransfer_rep/export', [App\Http\Controllers\Business\ReportController::class, 'stock_transfer_export'])->name('business.stockTransfer_rep.export');

    // Reports Low Stock
    Route::get('/reports/low_stock_rep/list', [App\Http\Controllers\Business\ReportController::class, 'lowStock_rep_list'])->name('business.lowStock_rep.list');
    Route::post('/reports/low_stock_rep/export', [App\Http\Controllers\Business\ReportController::class, 'lowStock_rep_export'])->name('business.lowStock_rep.export');

    // Reports Payment
    Route::get('/reports/payment_rep/list', [App\Http\Controllers\Business\ReportController::class, 'payment_rep_list'])->name('business.payment_rep.list');
    Route::post('/reports/payment_rep/export', [App\Http\Controllers\Business\ReportController::class, 'payment_rep_export'])->name('business.payment_rep.export');

    // Reports Writeoff
    Route::get('/reports/writeoff_rep/list', [App\Http\Controllers\Business\ReportController::class, 'writeoff_rep_list'])->name('business.writeoff_rep.list');
    Route::post('/reports/writeoff_rep/export', [App\Http\Controllers\Business\ReportController::class, 'writeoff_rep_export'])->name('business.writeoff_rep.export');
});
