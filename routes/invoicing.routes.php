<?php

/*
|--------------------------------------------------------------------------
| Anagami ERP - Фактуриране: референтни дефиниции на маршрути
|--------------------------------------------------------------------------
|
| Този файл е РЕФЕРЕНЦИЯ и не е предназначен за директно зареждане.
| Редовете са извлечени без промяна от routes/web.php, routes/web-settings.php
| и routes/web-public.php на Anagami ERP и показват кои маршрути принадлежат
| на модула. При интеграция ги пренесете в съответните групи на
| приемащото приложение (виж README.md, раздел "Интеграция").
|
| Необходими `use` декларации (контролери в App\Http\Controllers):
| BankAccount, CreditNote, Estimate, EstimateRequest, EstimateTemplate,
| Invoice, InvoiceFiles, InvoicePaymentDetail, InvoiceSetting,
| OfflinePaymentSetting, Order, Payment, PaymentGatewayCredential,
| ProductCategory, Product, ProductFile, ProductSubCategory, Proposal,
| ProposalTemplate, RecurringInvoice, TaxSetting, UnitType, Quickbook,
| QuickbookSettings и App\Http\Controllers\Payment\* (Stripe, Paypal, Mollie,
| Paystack, Flutterwave, Payfast, Authorize, Square, RazorPay, StripeWebhook).
|
| Публичните маршрути по-долу сочат към HomeController и PublicUrlController,
| които НЕ са част от модула (споделени са с останалата система).
*/

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. routes/web.php
|    Route::group(['middleware' => ['auth', 'multi-company-select', 'email_verified'], 'prefix' => 'account'], ...)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', 'multi-company-select', 'email_verified'], 'prefix' => 'account'], function () {

    /* PRODUCTS */
    Route::post('products/apply-quick-action', [ProductController::class, 'applyQuickAction'])->name('products.apply_quick_action');
    Route::post('products/remove-cart-item/{id}', [ProductController::class, 'removeCartItem'])->name('products.remove_cart_item');
    Route::get('products/options', [ProductController::class, 'allProductOption'])->name('products.options');


    Route::post('products/add-cart-item', [ProductController::class, 'addCartItem'])->name('products.add_cart_item');
    Route::get('products/cart', [ProductController::class, 'cart'])->name('products.cart');
    Route::get('products/empty-cart', [ProductController::class, 'emptyCart'])->name('products.empty_cart');

    /* Product Import */
    Route::group(
        ['prefix' => 'products'],
        function () {
            Route::get('import', [ProductController::class, 'importProduct'])->name('products.import');
            Route::post('import', [ProductController::class, 'importStore'])->name('products.import.store');
            Route::post('import/process', [ProductController::class, 'importProcess'])->name('products.import.process');
        }
    );

    Route::resource('products', ProductController::class);
    Route::resource('productCategory', ProductCategoryController::class);
    Route::get('getProductSubCategories/{id}', [ProductSubCategoryController::class, 'getSubCategories'])->name('get_product_sub_categories');
    Route::resource('productSubCategory', ProductSubCategoryController::class);

    /* PRODUCT FILES */
    Route::get('product-files/download/{id}', [ProductFileController::class, 'download'])->name('product-files.download');
    Route::post('product-files/delete-image/{id}', [ProductFileController::class, 'deleteImage'])->name('product-files.delete_image');
    Route::post('product-files/update-images', [ProductFileController::class, 'updateImages'])->name('product-files.update_images');
    Route::resource('product-files', ProductFileController::class);

    /* INVOICE FILES */
    Route::get('invoice-files/download/{id}', [InvoiceFilesController::class, 'download'])->name('invoice-files.download');
    Route::resource('invoice-files', InvoiceFilesController::class);


    /* Payments */
    Route::get('orders/offline-payment-modal', [OrderController::class, 'offlinePaymentModal'])->name('orders.offline_payment_modal');
    Route::get('orders/add-item', [OrderController::class, 'addItem'])->name('orders.add_item');
    Route::get('orders/stripe-modal', [OrderController::class, 'stripeModal'])->name('orders.stripe_modal');
    Route::post('orders/make-invoice/{orderId}', [OrderController::class, 'makeInvoice'])->name('orders.make_invoice');

    Route::post('orders/payment-failed/{orderId}', [OrderController::class, 'paymentFailed'])->name('orders.payment_failed');
    Route::post('orders/save-stripe-detail/', [OrderController::class, 'saveStripeDetail'])->name('orders.save_stripe_detail');
    Route::post('orders/change-status/', [OrderController::class, 'changeStatus'])->name('orders.change_status');
    /* Payments */
    Route::get('orders/download/{id}', [OrderController::class, 'download'])->name('orders.download');
    Route::post('orders/store-quantity/', [OrderController::class, 'storeQuantity'])->name('orders.store_quantity');


    /* Orders */
    Route::resource('orders', OrderController::class);

    // Invoices
    Route::get('invoices/offline-method-description', [InvoiceController::class, 'offlineDescription'])->name('invoices.offline_method_description');
    Route::get('invoices/offline-payment-modal', [InvoiceController::class, 'offlinePaymentModal'])->name('invoices.offline_payment_modal');
    Route::get('invoices/stripe-modal', [InvoiceController::class, 'stripeModal'])->name('invoices.stripe_modal');
    Route::post('invoices/save-stripe-detail/', [InvoiceController::class, 'saveStripeDetail'])->name('invoices.save_stripe_detail');
    Route::get('invoices/delete-image', [InvoiceController::class, 'deleteInvoiceItemImage'])->name('invoices.delete_image');
    Route::post('invoices/store-offline-payment', [InvoiceController::class, 'storeOfflinePayment'])->name('invoices.store_offline_payment');
    Route::post('invoices/store_file', [InvoiceController::class, 'storeFile'])->name('invoices.store_file');
    Route::get('invoices/file-upload', [InvoiceController::class, 'fileUpload'])->name('invoices.file_upload');
    Route::post('invoices/delete-applied-credit/{id}', [InvoiceController::class, 'deleteAppliedCredit'])->name('invoices.delete_applied_credit');
    Route::get('invoices/applied-credits/{id}', [InvoiceController::class, 'appliedCredits'])->name('invoices.applied_credits');
    Route::get('invoices/payment-reminder/{invoiceID}', [InvoiceController::class, 'remindForPayment'])->name('invoices.payment_reminder');
    Route::post('invoices/send-invoice/{invoiceID}', [InvoiceController::class, 'sendInvoice'])->name('invoices.send_invoice');
    Route::post('invoices/approve-offline-invoice/{invoiceID}', [InvoiceController::class, 'approveOfflineInvoice'])->name('invoices.approve_offline_invoice');
    Route::post('invoices/apply-quick-action', [InvoiceController::class, 'applyQuickAction'])->name('invoices.apply_quick_action');
    Route::get('invoices/download/{id}', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('invoices/add-item', [InvoiceController::class, 'addItem'])->name('invoices.add_item');
    Route::get('invoices/update-status/{invoiceID}', [InvoiceController::class, 'cancelStatus'])->name('invoices.update_status');
    Route::get('invoices/get-client-company/{projectID?}', [InvoiceController::class, 'getClientOrCompanyName'])->name('invoices.get_client_company');
    Route::post('invoices/fetchTimelogs', [InvoiceController::class, 'fetchTimelogs'])->name('invoices.fetch_timelogs');
    Route::get('invoices/check-shipping-address', [InvoiceController::class, 'checkShippingAddress'])->name('invoices.check_shipping_address');
    Route::get('invoices/product-category/{id}', [InvoiceController::class, 'productCategory'])->name('invoices.product_category');

    Route::get('invoices/toggle-shipping-address/{invoice}', [InvoiceController::class, 'toggleShippingAddress'])->name('invoices.toggle_shipping_address');
    Route::get('invoices/shipping-address-modal/{invoice}', [InvoiceController::class, 'shippingAddressModal'])->name('invoices.shipping_address_modal');
    Route::post('invoices/add-shipping-address/{clientId}', [InvoiceController::class, 'addShippingAddress'])->name('invoices.add_shipping_address');
    Route::get('invoices/get-exchange-rate/{id}', [InvoiceController::class, 'getExchangeRate'])->name('invoices.get_exchange_rate');

    Route::get('invoices/committed-modal', [InvoiceController::class, 'committedModal'])->name('invoices.committed_modal');

    Route::group(['prefix' => 'invoices'], function () {
        // Invoice recurring
        Route::post('recurring-invoice/change-status', [RecurringInvoiceController::class, 'changeStatus'])->name('recurring_invoice.change_status');
        Route::get('recurring-invoice/export/{startDate}/{endDate}/{status}/{employee}', [RecurringInvoiceController::class, 'export'])->name('recurring_invoice.export');
        Route::get('recurring-invoice/recurring-invoice/{id}', [RecurringInvoiceController::class, 'recurringInvoices'])->name('recurring_invoice.recurring_invoice');
        Route::delete('recurring-invoice/delete-repeat-invoices/{id}', [RecurringInvoiceController::class, 'deleteInvoices'])->name('recurring_invoice.delete_repeat_invoices');
        Route::resource('recurring-invoices', RecurringInvoiceController::class);
    });
    Route::resource('invoices', InvoiceController::class);

    Route::resource('invoices-payment-details', InvoicePaymentDetailController::class);

    // Estimates
    Route::get('estimates/delete-image', [EstimateController::class, 'deleteEstimateItemImage'])->name('estimates.delete_image');
    Route::get('estimates/download/{id}', [EstimateController::class, 'download'])->name('estimates.download');
    Route::post('estimates/send-estimate/{id}', [EstimateController::class, 'sendEstimate'])->name('estimates.send_estimate');
    Route::get('estimates/change-status/{id}', [EstimateController::class, 'changeStatus'])->name('estimates.change_status');
    Route::post('estimates/accept/{id}', [EstimateController::class, 'accept'])->name('estimates.accept');
    Route::post('estimates/decline/{id}', [EstimateController::class, 'decline'])->name('estimates.decline');
    Route::get('estimates/add-item', [EstimateController::class, 'addItem'])->name('estimates.add_item');
    Route::resource('estimates', EstimateController::class);


    // Proposals
    Route::get('proposals/delete-image', [ProposalController::class, 'deleteProposalItemImage'])->name('proposals.delete_image');
    Route::get('proposals/download/{id}', [ProposalController::class, 'download'])->name('proposals.download');
    Route::post('proposals/send-proposal/{id}', [ProposalController::class, 'sendProposal'])->name('proposals.send_proposal');
    Route::get('proposals/add-item', [ProposalController::class, 'addItem'])->name('proposals.add_item');
    Route::resource('proposals', ProposalController::class);

    // Proposal Template
    Route::post('proposal-template/apply-quick-action', [ProposalTemplateController::class, 'applyQuickAction'])->name('proposal_template.apply_quick_action');
    Route::get('proposal-template/add-item', [ProposalController::class, 'addItem'])->name('proposal-template.add_item');
    Route::resource('proposal-template', ProposalTemplateController::class);
    Route::get('proposal-template/download/{id}', [ProposalTemplateController::class, 'download'])->name('proposal-template.download');
    Route::get('proposals-template/delete-image', [ProposalTemplateController::class, 'deleteProposalItemImage'])->name('proposal_template.delete_image');

    // Payments
    Route::post('payments/apply-quick-action', [PaymentController::class, 'applyQuickAction'])->name('payments.apply_quick_action');
    Route::get('payments/download/{id}', [PaymentController::class, 'download'])->name('payments.download');
    Route::get('payments/account-list', [PaymentController::class, 'accountList'])->name('payments.account_list');
    Route::get('payments/offline-payments', [PaymentController::class, 'offlineMethods'])->name('offline.methods');
    Route::get('payments/add-bulk-payments', [PaymentController::class, 'addBulkPayments'])->name('payments.add_bulk_payments');
    Route::post('payments/save-bulk-payments', [PaymentController::class, 'saveBulkPayments'])->name('payments.save_bulk_payments');

    Route::resource('payments', PaymentController::class)->except(['edit', 'update']);

    // Credit notes
    Route::post('creditnotes/store_file', [CreditNoteController::class, 'storeFile'])->name('creditnotes.store_file');
    Route::get('creditnotes/file-upload', [CreditNoteController::class, 'fileUpload'])->name('creditnotes.file_upload');
    Route::post('creditnotes/delete-credited-invoice/{id}', [CreditNoteController::class, 'deleteCreditedInvoice'])->name('creditnotes.delete_credited_invoice');
    Route::get('creditnotes/credited-invoices/{id}', [CreditNoteController::class, 'creditedInvoices'])->name('creditnotes.credited_invoices');
    Route::post('creditnotes/apply-invoice-credit/{id}', [CreditNoteController::class, 'applyInvoiceCredit'])->name('creditnotes.apply_invoice_credit');
    Route::get('creditnotes/apply-to-invoice/{id}', [CreditNoteController::class, 'applyToInvoice'])->name('creditnotes.apply_to_invoice');
    Route::get('creditnotes/download/{id}', [CreditNoteController::class, 'download'])->name('creditnotes.download');

    Route::get('creditnotes/convert-invoice/{id}', [CreditNoteController::class, 'convertInvoice'])->name('creditnotes.convert-invoice');

    Route::resource('creditnotes', CreditNoteController::class);

    // Bank account
    Route::post('bankaccount/apply-quick-action', [BankAccountController::class, 'applyQuickAction'])->name('bankaccounts.apply_quick_action');
    Route::post('bankaccount/apply-transaction-quick-action', [BankAccountController::class, 'applyTransactionQuickAction'])->name('bankaccounts.apply_transaction_quick_action');
    Route::get('bankaccounts/create-transaction', [BankAccountController::class, 'createTransaction'])->name('bankaccounts.create_transaction');
    Route::post('bankaccount/store-transaction', [BankAccountController::class, 'storeTransaction'])->name('bankaccounts.store_transaction');
    Route::post('bankaccount/change-status', [BankAccountController::class, 'changeStatus'])->name('bankaccounts.change_status');

    Route::get('bankaccounts/view-transaction/{id}', [BankAccountController::class, 'viewTransaction'])->name('bankaccounts.view_transaction');
    Route::post('bankaccount/destroy-transaction', [BankAccountController::class, 'destroyTransaction'])->name('bankaccounts.destroy_transaction');
    Route::get('bankaccount/generate-statement/{id}', [BankAccountController::class, 'generateStatement'])->name('bankaccounts.generate_statement');
    Route::get('bankaccount/get-bank-statement', [BankAccountController::class, 'getBankStatement'])->name('bankaccounts.get_bank_statement');

    Route::resource('bankaccounts', BankAccountController::class);

    // Estimate Template
    Route::get('estimate-template/add-item', [EstimateTemplateController::class, 'addItem'])->name('estimate-template.add_item');
    Route::resource('estimate-template', EstimateTemplateController::class);
    Route::get('estimates-template/delete-image', [EstimateTemplateController::class, 'deleteEstimateItemImage'])->name('estimate-template.delete_image');
    Route::get('estimate-template/download/{id}', [EstimateTemplateController::class, 'download'])->name('estimate-template.download');

    Route::get('quickbooks/{hash}/callback', [QuickbookController::class, 'callback'])->name('quickbooks.callback');
    Route::get('quickbooks', [QuickbookController::class, 'index'])->name('quickbooks.index');

    // Estimate Request
    Route::post('estimate-request/apply-quick-action', [EstimateRequestController::class, 'applyQuickAction'])->name('estimate-request.apply_quick_action');
    Route::post('estimate-request/change-status/', [EstimateRequestController::class, 'changeStatus'])->name('estimate-request.change_status');
    Route::get('estimate-request-confirm-rejected/{id}', [EstimateRequestController::class, 'rejectConfirmation'])->name('estimate-request.confirm_rejected');
    Route::get('estimate-request/send-estimate-request', [EstimateRequestController::class, 'sendEstimateRequest'])->name('estimate-request.send_estimate_request');
    Route::post('estimate-request/send_estimate_mail', [EstimateRequestController::class, 'sendEstimateMail'])->name('estimate-request.send_estimate_mail');
    Route::resource('estimate-request', EstimateRequestController::class);
});

/*
|--------------------------------------------------------------------------
| 2. routes/web-settings.php
|    Route::group(['middleware' => 'auth', 'prefix' => 'account/settings'], ...)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth', 'prefix' => 'account/settings'], function () {

    Route::resource('payment-gateway-settings', PaymentGatewayCredentialController::class);
    /* End Currency Settings routes */

    Route::resource('offline-payment-setting', OfflinePaymentSettingController::class);

    /* Invoice Setting Routes */
    Route::post('invoice-settings/update-template/{id}', [InvoiceSettingController::class, 'updateTemplate'])->name('invoice_settings.update_template');
    Route::post('invoice-settings/update-prefix/{id}', [InvoiceSettingController::class, 'updatePrefix'])->name('invoice_settings.update_prefix');
    Route::resource('invoice-settings', InvoiceSettingController::class);

    /* unitType */
    Route::resource('unit-type', UnitTypeController::class);
    Route::post('unit-types/set-default', [UnitTypeController::class, 'setDefaultUnit'])->name('unit-type.set_default');

    // Tax Settings
    Route::put('taxes/{tax}/to-keep-under-total-payment', [TaxSettingController::class, 'updateToKeepUnderTotalPayment'])->name('taxes.update_to_keep_under_total_payment');
    Route::resource('taxes', TaxSettingController::class);

    Route::resource('quickbooks-settings', QuickbookSettingsController::class);
});

/*
|--------------------------------------------------------------------------
| 3. routes/web-public.php (без middleware за автентикация)
|    Публични страници за фактури/оферти/предложения и платежни шлюзове
|--------------------------------------------------------------------------
*/
Route::get('/invoice-stripe/stripe-modal/', [HomeController::class, 'stripeModal'])->name('front.stripe_modal');
Route::get('/invoice-paystack/paystack-modal/', [HomeController::class, 'paystackModal'])->name('front.paystack_modal');
Route::get('/invoice-flutterwave/flutterwave-modal/', [HomeController::class, 'flutterwaveModal'])->name('front.flutterwave_modal');
Route::get('/invoice-mollie/mollie-modal/', [HomeController::class, 'mollieModal'])->name('front.mollie_modal');
Route::get('/invoice-authorize/authorize-modal/', [HomeController::class, 'authorizeModal'])->name('front.authorize_modal');
Route::post('invoice-stripe/save-stripe-detail/', [HomeController::class, 'saveStripeDetail'])->name('front.save_stripe_detail');
Route::get('/invoice/download/{id}', [HomeController::class, 'downloadInvoice'])->name('front.invoice_download');
Route::post('/invoice-payment-failed/{invoiceId}', [HomeController::class, 'invoicePaymentfailed'])->name('front.invoice_payment_failed');

// Estimate Public url

Route::post('/estimate/decline/{id}', [PublicUrlController::class, 'estimateDecline'])->name('front.estimate.decline');
Route::post('/estimate/accept/{id}', [PublicUrlController::class, 'estimateAccept'])->name('front.estimate.accept');
Route::get('/estimate/download/{id}', [PublicUrlController::class, 'estimateDownload'])->name('front.estimate.download');

Route::post('/proposal-action/{id}', [HomeController::class, 'proposalActionStore'])->name('front.proposal_action');
Route::get('/proposal/download/{id}', [HomeController::class, 'downloadProposal'])->name('front.download_proposal');

// Payment routes
Route::post('stripe/{invoiceId}', [StripeController::class, 'paymentWithStripe'])->name('stripe');
Route::post('stripe-public/{hash}', [StripeController::class, 'paymentWithStripePublic'])->name('stripe_public');

Route::post('paystack-public/{id}/{hash}', [PaystackController::class, 'paymentWithPaystackPublic'])->name('paystack_public');
Route::get('paystack/callback/{id}/{type}/{hash}', [PaystackController::class, 'handleGatewayCallback'])->name('paystack.callback');
Route::post('paystack-webhook/{hash}', [PaystackController::class, 'handleGatewayWebhook'])->name('paystack.webhook');
Route::get('paystack-webhook/{hash}', [PaystackController::class, 'getWebhook'])->name('get_paystack.webhook');

Route::post('flutterwave-public/{id}', [FlutterwaveController::class, 'paymentWithFlutterwavePublic'])->name('flutterwave_public');
Route::get('flutterwave/callback/{id}/{type}/{hash}', [FlutterwaveController::class, 'handleGatewayCallback'])->name('flutterwave.callback');
Route::post('flutterwave-webhook/{hash}', [FlutterwaveController::class, 'handleGatewayWebhook'])->name('flutterwave.webhook');
Route::get('flutterwave-webhook/{hash}', [FlutterwaveController::class, 'getWebhook'])->name('get_flutterwave.webhook');

Route::post('mollie-public/{id}/{hash}', [MollieController::class, 'paymentWithMolliePublic'])->name('mollie_public');
Route::get('mollie/callback/{id}/{type}/{hash}', [MollieController::class, 'handleGatewayCallback'])->name('mollie.callback');
Route::post('mollie-webhook/{hash}', [MollieController::class, 'handleGatewayWebhook'])->name('mollie.webhook');
Route::get('mollie-webhook/{hash}', [MollieController::class, 'getWebhook'])->name('get_mollie.webhook');

Route::post('payfast-public', [PayfastController::class, 'paymentWithPayfastPublic'])->name('payfast_public');
Route::get('payfast/callback/{id}/{type}/{status}', [PayfastController::class, 'handleGatewayCallback'])->name('payfast.callback');
Route::post('payfast-webhook/{hash}', [PayfastController::class, 'handleGatewayWebhook'])->name('payfast.webhook');
Route::get('payfast-webhook/{hash}', [PayfastController::class, 'getWebhook'])->name('get_payfast.webhook');

Route::post('authorize-public/{id}', [AuthorizeController::class, 'paymentWithAuthorizePublic'])->name('authorize_public');

Route::post('square-public', [SquareController::class, 'paymentWithSquarePublic'])->name('square_public');
Route::get('square/callback/{id}/{type}/{hash}', [SquareController::class, 'handleGatewayCallback'])->name('square.callback');
Route::post('square-webhook/{hash}', [SquareController::class, 'handleGatewayWebhook'])->name('square.webhook');

Route::post('pay-with-razorpay/{hash}', [RazorPayController::class, 'payWithRazorPay'])->name('pay_with_razorpay');
Route::post('razorpay-webhook/{hash}', [RazorPayController::class, 'handleGatewayWebhook'])->name('razorpay.webhook');
Route::get('razorpay-webhook/{hash}', [PaypalController::class, 'getWebhook'])->name('get_razorpay.webhook');

Route::get('paypal-public/{invoiceId}', [PaypalController::class, 'paymentWithpaypalPublic'])->name('paypal_public');
Route::get('paypal/{invoiceId}', [PaypalController::class, 'paymentWithpaypal'])->name('paypal');
Route::get('paypal', [PaypalController::class, 'getPaymentStatus'])->name('get_paypal_status');
Route::get('paypal-recurring', [PaypalController::class, 'payWithPaypalRecurring'])->name('paypal_recurring');

// Paypal IPN
Route::post('paypal-webhook/{hash}', [PaypalController::class, 'webhook'])->name('paypal.webhook');
Route::get('paypal-webhook/{hash}', [PaypalController::class, 'getWebhook'])->name('get_paypal.webhook');

// Stripe webhook
Route::get('/verify-webhook/{hash?}', [StripeWebhookController::class, 'getWebhook'])->name('get_stripe_webhook');
Route::post('/verify-webhook/{hash}', [StripeWebhookController::class, 'verifyStripeWebhook'])->name('stripe.webhook');

// SIGNED URLS ->middleware('signed')
Route::get('/proposal/{hash}', [HomeController::class, 'proposal'])->name('front.proposal')->middleware('signed');
Route::get('/estimate/{hash}', [PublicUrlController::class, 'estimateView'])->name('front.estimate.show')->middleware('signed');
Route::get('/invoice/{hash}', [HomeController::class, 'invoice'])->name('front.invoice')->middleware('signed');
