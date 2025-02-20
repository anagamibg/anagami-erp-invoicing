# Anagami ERP – модул „Фактуриране“

Изнесен (екстрахиран) модул за **фактуриране и свързаните с него финансови документи** от Anagami ERP – мултитенант бизнес платформа на Laravel (PHP 8.3, Blade, Bootstrap 4, jQuery, Yajra DataTables, DomPDF).

Репозиторият **не е самостоятелно приложение**. Той съдържа само файловете, свързани с фактурирането, и запазва оригиналните им относителни пътища (`app/…`, `resources/…`, `database/…`), така че да може да се копира директно върху Laravel проекта на Anagami ERP.

> Частен репозиторий за вътрешно ползване. Вж. раздел [Забележка за ползване](#забележка-за-ползване).

---

## Съдържание

1. [Функционалности](#функционалности)
2. [Структура на репозитория](#структура-на-репозитория)
3. [Зависимости от останалата система](#зависимости-от-останалата-система)
4. [Изисквания](#изисквания)
5. [Интеграция](#интеграция)
6. [Планирани задачи (cron)](#планирани-задачи-cron)
7. [Какво не е включено](#какво-не-е-включено)
8. [Забележка за ползване](#забележка-за-ползване)

---

## Функционалности

Изброените възможности са изведени от реалния код (контролери, модели, изгледи, миграции) в този репозиторий.

### Фактури
- Създаване, редакция, преглед, изтегляне (PDF) и изтриване на фактури (`InvoiceController`, `InvoicesDataTable`).
- Изпращане на фактура към клиента, напомняне за плащане, отказ/анулиране на статус (`sendInvoice`, `remindForPayment`, `cancelStatus`).
- Фактури въз основа на отчетено време (timelog фактури) – избор на период и зареждане на записи от проектите (`fetchTimelogs`, изглед `invoices/ajax/create-timelog-invoice`).
- Работа с различни валути и курс на обмен (`getExchangeRate`), адрес за доставка (добавяне, превключване, проверка).
- Прикачени файлове към фактура (`InvoiceFilesController`, `InvoiceFiles`) и изображения към артикулите (`InvoiceItemImage`).
- Приложени кредитни известия към фактура (`applied_credits`, `deleteAppliedCredit`).
- Групови (quick) действия върху списъка с фактури.
- Публичен изглед на фактура (`resources/views/invoice.blade.php`) за достъп от клиента.

### Повтарящи се фактури
- Шаблони за периодично фактуриране (`RecurringInvoice`, `RecurringInvoiceItems`), смяна на статус, преглед на генерираните фактури, експорт и изтриване на повтарящите се фактури (`RecurringInvoiceController`).
- Автоматично създаване чрез конзолна команда `recurring-invoice-create` (`AutoCreateRecurringInvoices`).
- Автоматични напомняния преди и след падеж чрез командата `send-invoice-reminder` (`SendInvoiceReminder`).

### Кредитни известия
- Създаване/редакция на кредитни известия със собствена номерация и PDF (`CreditNoteController`, `CreditNotes`).
- Приложение на кредит към една или няколко фактури, преглед на кредитираните фактури, конвертиране към фактура (`applyToInvoice`, `applyInvoiceCredit`, `creditedInvoices`, `convertInvoice`).
- Качване на файлове към кредитно известие.

### Оферти (Estimates), шаблони и заявки за оферта
- Оферти с артикули, статус, изпращане към клиента, приемане (с подпис – `AcceptEstimate`) и отказ (`EstimateController`).
- Шаблони за оферти (`EstimateTemplate*`) и заявки за оферта (`EstimateRequest`) с потвърждаване/отхвърляне и изпращане на покана по имейл.
- Публичен изглед на оферта (`resources/views/estimate.blade.php`).

### Предложения (Proposals)
- Търговски предложения към лийдове и шаблони за предложения (`ProposalController`, `ProposalTemplateController`).
- Опция за изискване на подпис (`signature_approval`), записване на подписа (`ProposalSign`), изпращане и PDF.
- Публичен изглед на предложение (`resources/views/proposal.blade.php`) и таб „Предложения“ в лийд (`leads/ajax/proposal`).

### Плащания
- Регистриране на плащания към фактури, групово добавяне на плащания, PDF разписка, избор на банкова сметка (`PaymentController`, `Payment`).
- Подробности за плащане по фактура (`InvoicePaymentDetail`, `InvoicePaymentDetailController`).
- Офлайн методи на плащане (`OfflinePaymentMethod`, `OfflinePaymentSettingController`) и одобрение на офлайн плащане.
- Онлайн платежни шлюзове – контролери в `app/Http/Controllers/Payment/`: Stripe (+ webhook), PayPal, Mollie, Paystack, Flutterwave, PayFast, Authorize.Net, Square, Razorpay. Настройки на шлюзовете: `PaymentGatewayCredentialController` / `PaymentGatewayCredentials` (чувствителните полета са декларирани като `encrypted` в модела).

### Банкови сметки и транзакции
- Банкови и касови сметки; за банковите – тип на сметката (спестовна, разплащателна, кредитна карта, заем, овърдрафт) – и транзакции, смяна на статус (`BankAccountController`, `BankAccount`, `BankTransaction`).
- Генериране на банково извлечение в PDF (`bank-account/pdf/statement`), диаграма „кредит срещу дебит“.

### Поръчки и продукти
- Поръчки (`Order`, `OrderItems`, `OrderCart`) с количка, плащане (Stripe/офлайн), смяна на статус и **конвертиране на поръчка във фактура** (`makeInvoice`, `MakeOrderInvoiceTrait`).
- Продукти с категории и подкатегории, файлове/изображения, SKU, **импорт от Excel** през опашка (`ProductImport`, `ImportProductJob`).
- Мерни единици (`UnitType`, `UnitTypeController`, `UnitTypeSaveTrait`).

### Данъци
- Данъчни ставки (`Tax`, `TaxSettingController`) с флаг дали данъкът се включва в общата сума за плащане (миграция `include_in_total_payment_to_taxes`).

### Настройки на фактурирането
- Префикси/номерация (фактури, оферти, кредитни известия, поръчки, заявки за оферта, предложения), разделител, избор на PDF шаблон (5 шаблона), допълнителна информация, мерни единици и платежни методи (`InvoiceSettingController`, `InvoiceSetting`, изгледи в `resources/views/invoice-settings/`).

### Интеграция с QuickBooks
- Настройки и OAuth2 връзка с QuickBooks Online, синхронизация на клиенти, артикули, фактури и плащания (`QuickbookController`, `QuickbookSettingsController`, `QuickBooksSetting`).

### Известия
- 19 събития → 19 слушателя → 23 класа известия (имейл и др. канали, наследени от `BaseNotification` на основната система) за нови/променени фактури, плащания, напомняния, оферти, заявки за оферта, предложения, кредитни известия, поръчки.

### PDF шаблони
- По 5 шаблона (`invoice-1` … `invoice-5`) за фактури, оферти, предложения, кредитни известия и поръчки, плюс шаблони за повтарящи се документи и PDF на плащания и банково извлечение – общо 33 файла в `pdf/` директориите.

---

## Структура на репозитория

```
anagami-erp-invoicing/
├── app/
│   ├── Console/Commands/        # recurring-invoice-create, send-invoice-reminder
│   ├── DataTables/              # Yajra DataTables класове
│   ├── Events/  Listeners/      # събития и слушатели
│   ├── Http/
│   │   ├── Controllers/         # + Payment/ (платежни шлюзове)
│   │   └── Requests/            # FormRequest валидации
│   ├── Imports/  Jobs/          # импорт на продукти
│   ├── Models/                  # Eloquent модели
│   ├── Notifications/           # известия
│   ├── Observers/               # observers (бизнес логика по жизнения цикъл)
│   └── Traits/                  # MakeOrderInvoice, MakePayment, PaymentGateway, UnitTypeSave
├── database/
│   ├── migrations/              # миграции, свързани с фактурирането
│   ├── seeders/                 # BankAccount, Estimate, Product, Tax
│   └── factories/               # ProductFactory
├── docs/
│   └── event-service-provider.reference.php   # какво да се регистрира в EventServiceProvider
├── resources/
│   ├── lang/eng/                # само релевантните ключове
│   ├── sass/redesign/pages/     # _invoices.scss
│   ├── scss/                    # invoice.scss, create-invoice.scss
│   └── views/                   # Blade изгледи (вкл. pdf/ шаблони)
└── routes/
    └── invoicing.routes.php     # референция за маршрутите (не се зарежда директно)
```

### Брой файлове по категории

| Категория | Път | Файлове |
|---|---|---:|
| Контролери | `app/Http/Controllers` (24 + 10 в `Payment/`) | 34 |
| Form Requests | `app/Http/Requests` | 44 |
| Модели | `app/Models` | 43 |
| Observers | `app/Observers` | 23 |
| Събития (Events) | `app/Events` | 19 |
| Слушатели (Listeners) | `app/Listeners` | 19 |
| Известия (Notifications) | `app/Notifications` | 23 |
| DataTables | `app/DataTables` | 14 |
| Конзолни команди | `app/Console/Commands` | 2 |
| Импорти и Jobs | `app/Imports`, `app/Jobs` | 2 |
| Traits | `app/Traits` | 4 |
| Миграции | `database/migrations` | 49 |
| Seeders | `database/seeders` | 4 |
| Factories | `database/factories` | 1 |
| Blade изгледи (вкл. PDF и известия) | `resources/views` | 210 |
| Езикови файлове (EN, филтрирани) | `resources/lang/eng` | 6 |
| SCSS | `resources/scss`, `resources/sass/redesign/pages` | 3 |
| Маршрути (референция) | `routes` | 1 |
| Референция за регистрация на observers/events | `docs` | 1 |
| Служебни (README, .gitignore) | корен | 2 |
| **Общо** | | **504** |

### Изгледи по области (`resources/views`)

| Директория | Файлове | Директория | Файлове |
|---|---:|---|---:|
| `invoices` | 28 | `proposal-template` | 8 |
| `notifications` (избрани) | 18 | `payments` | 8 |
| `credit-notes` | 17 | `recurring-invoices` | 7 |
| `orders` | 16 | `estimate-requests` | 7 |
| `proposals` | 14 | `public-payment` | 6 |
| `estimates` | 13 | `estimates-templates` | 6 |
| `payment-gateway-settings` | 13 | `clients` (таби) | 5 |
| `products` | 11 | `projects` (таби) | 4 |
| `invoice-settings` | 11 | `tax-settings` | 3 |
| `bank-account` | 10 | `tax`, `leads` (таб) | 1 + 1 |
| публични: `invoice`, `estimate`, `proposal` | 3 | | |

---

## Зависимости от останалата система

Модулът **не е самодостатъчен**. Следните части на Anagami ERP **не са включени** и трябва да присъстват в приемащото приложение.

**Споделени трейтове и скоупове (мултитенантност)**
- `App\Traits\HasCompany` и `App\Scopes\CompanyScope` – филтриране по `company_id` (почти всички модели ги ползват).
- `App\Scopes\ActiveScope`, `App\Traits\CustomFieldsTrait`, `CustomFieldsRequestTrait`, `HasMaskImage`, `IconTrait`, `UniversalSearchTrait`, `ExcelImportable`, `ImportExcel`, `DealHistoryTrait`, `EmployeeActivityTrait`.

**Базови класове и помощни функции**
- `App\Models\BaseModel`, `App\Http\Controllers\Controller`, `AccountBaseController`, `App\Http\Requests\CoreRequest`.
- `app/Helper/start.php` (глобални функции като `company()`, `user()`, `global_setting()`, `invoice_setting()`, проверки на права и лимити на пакетите), както и `App\Helper\Common`, `Files`, `NumberFormat`, `Reply`, `UserService`.

**Споделени модели**
- `Company`, `CompanyAddress`, `User`, `ClientDetails`, `ClientContact`, `Currency`, `Project`, `ProjectTimeLog`, `ProjectMilestone`, `ProjectActivity`, `Task`, `Lead`, `Deal`, `Contract`, `Ticket`, `Expense`, `ExpenseRecurring`, `CustomField`, `CustomFieldGroup`, `GlobalSetting`, `Module`, `ModuleSetting`, `Permission`, `PermissionRole`, `Role`, `UserPermission`, `Notification`, `EmailNotificationSetting`, `UniversalSearch`, `GoogleCalendarModule`.

**Инфраструктура**
- `App\Providers\EventServiceProvider` (регистрация на observers и listeners), `App\Console\Kernel` (планиране), `BaseNotification` и каналите за известия, middleware (`auth`, `multi-company-select`, `email_verified`, `check-company-package`), `routes/web.php`, `web-settings.php`, `web-public.php`.
- Публичните страници за фактура/оферта/предложение и платежните callback-и минават през `HomeController` и `PublicUrlController`, които не са част от модула.
- Основните таблици (`invoices`, `invoice_items`, `payments`, `credit_notes`, `estimates`, `proposals`, `orders`, `products`, `taxes` и др.) се създават от базовите миграции на Anagami ERP (`2018_01_01_000000_create_worksuite_new_table.php`), които не са включени; тук са само по-късните миграции, които ги създават допълнително или променят.

**Незадължителни препратки**
- Кодът препраща към `Modules\Purchase\…` (`PurchaseProduct`, `PurchaseStockAdjustment` в `InvoiceController` и `Product`). Този модул не е част от системата в момента и не е включен.
- `Froiden\RestAPI\Exceptions\ApiException` се ползва в части от кода – пакетът е в `composer.json` на основното приложение.

**Composer пакети (от основното приложение), използвани от модула**
`yajra/laravel-datatables-*`, `barryvdh/laravel-dompdf` / `dompdf/dompdf`, `maatwebsite/excel`, `stripe/stripe-php`, `paypal/rest-api-sdk-php`, `mollie/laravel-mollie`, `unicodeveloper/laravel-paystack`, `kingflamez/laravelrave` (Flutterwave), `billowapp/payfast`, `authorizenet/authorizenet`, `square/square`, `razorpay/razorpay`, `quickbooks/v3-php-sdk`, `laravel-notification-channels/onesignal`.

---

## Изисквания

- **PHP** ≥ 8.2 (системата работи на 8.3)
- **Laravel** 10 или по-нов (основното приложение е на Laravel 10; текущият `composer.json` на Anagami ERP изисква `^12.0`)
- **База данни**: MariaDB или MySQL (миграциите съдържат и суров SQL, напр. `ALTER TABLE … ENUM`)
- Работеща инсталация на Anagami ERP със споделените компоненти от предишния раздел
- Инсталирани composer пакети (виж по-горе), опашка (queue) за импорт на продукти и изпращане на известия, cron за планираните команди

---

## Интеграция

1. **Копиране на файловете** – копирайте съдържанието на репозитория върху корена на Laravel проекта (запазват се пътищата):
   ```bash
   rsync -a --exclude='.git' --exclude='README.md' --exclude='.gitignore' \
         --exclude='routes/' --exclude='docs/' \
         ./ /път/към/anagami-erp/
   ```
   Внимание: `resources/lang/eng/*.php` са **филтрирани подмножества** на езиковите файлове. Не ги презаписвайте върху пълните файлове – обединете ключовете (напр. с `array_merge`) или ръчно.

2. **Миграции** – ако се инсталира върху нова база, базовите таблици идват от основните миграции на Anagami ERP; след тях изпълнете миграциите на модула:
   ```bash
   php artisan migrate
   ```
   В съществуваща инсталация миграциите вероятно вече са изпълнени – проверете таблицата `migrations`.

3. **Маршрути** – `routes/invoicing.routes.php` е **референция**, извлечена от `routes/web.php`, `routes/web-settings.php` и `routes/web-public.php`. Пренесете дефинициите в съответните групи (автентикирани, настройки, публични) и добавете нужните `use` декларации за контролерите.

4. **Observers и Events** – регистрирайте ги в `App\Providers\EventServiceProvider` (свойства `$listen` и `$observers`). Пълният списък с 19 събития и 23 observers е в `docs/event-service-provider.reference.php`.

5. **Планирани задачи** – добавете в `App\Console\Kernel` (виж следващия раздел).

6. **Езикови ключове** – слейте ключовете от `resources/lang/eng/` с наличните в системата (за други езици – съответните преводи, които не са част от този репозиторий).

7. **Seeders** (по желание) – `BankAccountSeeder`, `EstimateSeeder`, `ProductTableSeeder`, `TaxTableSeeder` извикайте от `DatabaseSeeder`/`CoreDatabaseSeeder` по ваша преценка.

8. **Кешове** – след интеграция:
   ```bash
   composer dump-autoload
   php artisan optimize:clear
   php artisan view:clear
   ```

---

## Планирани задачи (cron)

В оригиналния `App\Console\Kernel` командите са планирани така:

```php
$schedule->command('recurring-invoice-create')->dailyAt('01:30');
$schedule->command('send-invoice-reminder')->dailyAt('01:50');
```

Нужен е стандартен Laravel cron запис (`* * * * * php artisan schedule:run`).

---

## Какво не е включено

- Фактурирането за **SaaS абонаментите на самата платформа** (`app/Models/SuperAdmin/*Invoice*`, `GlobalInvoice*`, `app/Http/Controllers/SuperAdmin/*`, `resources/views/super-admin/*`) – това е отделен домейн (billing на пакети).
- Отчети (`FinanceReportController`, `SalesReportDataTable` и др.), табла (dashboard widgets) и универсалното търсене.
- Таблицата `lead_products` (свързва продукти с лийдове – част от CRM).
- Големите споделени/агрегирани миграции (`create_worksuite_new_table`, `create_other_migration_till_date_table`, `add_company_id_in_all_table` и др.).
- Публични ресурси (`public/`), компилирани стилове/JS, `vendor/`, `node_modules/`, `.env`, конфигурации и каквито и да е тайни/ключове.

---

## Забележка за ползване

Това е **частен репозиторий за вътрешно ползване** в рамките на Anagami. Съдържанието е извадка от кода на Anagami ERP и не е предназначено за публично разпространение. Файловете не съдържат ключове, пароли или адреси на сървъри; платежните настройки се съхраняват в базата данни (криптирани полета) и не са част от кода.
