<?php

/*
| Референция: регистрации в App\Providers\EventServiceProvider, които принадлежат на модула
| Фактуриране. Копирайте записите в свойствата $listen и $observers на приемащото приложение.
*/

return [
    'listen' => [
        \App\Events\NewEstimateEvent::class => [\App\Listeners\NewEstimateListener::class],
        \App\Events\NewInvoiceEvent::class => [\App\Listeners\NewInvoiceListener::class],
        \App\Events\InvoiceUpdatedEvent::class => [\App\Listeners\InvoiceUpdatedListener::class],
        \App\Events\InvoicePaymentReceivedEvent::class => [\App\Listeners\InvoicePaymentReceivedListener::class],
        \App\Events\NewPaymentEvent::class => [\App\Listeners\NewPaymentListener::class],
        \App\Events\PaymentReminderEvent::class => [\App\Listeners\PaymentReminderListener::class],
        \App\Events\EstimateDeclinedEvent::class => [\App\Listeners\EstimateDeclinedListener::class],
        \App\Events\NewProposalEvent::class => [\App\Listeners\NewProposalListener::class],
        \App\Events\NewInvoiceRecurringEvent::class => [\App\Listeners\NewInvoiceRecurringListener::class],
        \App\Events\NewCreditNoteEvent::class => [\App\Listeners\NewCreditNoteListener::class],
        \App\Events\NewProductPurchaseEvent::class => [\App\Listeners\NewProductPurchaseListener::class],
        \App\Events\InvoiceReminderEvent::class => [\App\Listeners\InvoiceReminderListener::class],
        \App\Events\InvoiceReminderAfterEvent::class => [\App\Listeners\InvoiceReminderAfterListener::class],
        \App\Events\NewOrderEvent::class => [\App\Listeners\NewOrderListener::class],
        \App\Events\OrderUpdatedEvent::class => [\App\Listeners\OrderUpdatedListener::class],
        \App\Events\EstimateAcceptedEvent::class => [\App\Listeners\EstimateAcceptedListener::class],
        \App\Events\EstimateRequestRejectedEvent::class => [\App\Listeners\EstimateRequestRejectedListener::class],
        \App\Events\EstimateRequestAcceptedEvent::class => [\App\Listeners\EstimateRequestAcceptedListener::class],
        \App\Events\NewEstimateRequestEvent::class => [\App\Listeners\NewEstimateRequestListener::class],
    ],

    'observers' => [
        \App\Models\CreditNotes::class => [\App\Observers\CreditNoteObserver::class],
        \App\Models\Estimate::class => [\App\Observers\EstimateObserver::class],
        \App\Models\Invoice::class => [\App\Observers\InvoiceObserver::class],
        \App\Models\InvoiceSetting::class => [\App\Observers\InvoiceSettingObserver::class],
        \App\Models\Order::class => [\App\Observers\OrderObserver::class],
        \App\Models\Payment::class => [\App\Observers\PaymentObserver::class],
        \App\Models\Product::class => [\App\Observers\ProductObserver::class],
        \App\Models\ProductFiles::class => [\App\Observers\ProductFileObserver::class],
        \App\Models\Proposal::class => [\App\Observers\ProposalObserver::class],
        \App\Models\RecurringInvoice::class => [\App\Observers\InvoiceRecurringObserver::class],
        \App\Models\InvoiceFiles::class => [\App\Observers\InvoiceFileObserver::class],
        \App\Models\Tax::class => [\App\Observers\TaxObserver::class],
        \App\Models\ProposalTemplate::class => [\App\Observers\ProposalTemplateObserver::class],
        \App\Models\BankAccount::class => [\App\Observers\BankAccountObserver::class],
        \App\Models\BankTransaction::class => [\App\Observers\BankTransactionObserver::class],
        \App\Models\OfflinePaymentMethod::class => [\App\Observers\OfflinePaymentMethodObserver::class],
        \App\Models\PaymentGatewayCredentials::class => [\App\Observers\PaymentGatewayCredentialsObserver::class],
        \App\Models\ProductCategory::class => [\App\Observers\ProductCategoryObserver::class],
        \App\Models\ProductSubCategory::class => [\App\Observers\ProductSubCategoryObserver::class],
        \App\Models\AcceptEstimate::class => [\App\Observers\AcceptEstimateObserver::class],
        \App\Models\EstimateTemplate::class => [\App\Observers\EstimateTemplateObserver::class],
        \App\Models\UnitType::class => [\App\Observers\UnitTypeObserver::class],
        \App\Models\EstimateRequest::class => [\App\Observers\EstimateRequestObserver::class],
    ],
];
