# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Laravel 10 admin panel (PHP 8.1+) for an online gambling/casino backend. The app manages members, transactions, deposits/withdrawals, game providers (AMB, JILI, JDB, TF, SA, Sexy, WM, PG Hard, UFA, SBO), promotions, partner/affiliate programs, and reporting. Frontend is server-rendered Blade with Tailwind, Alpine.js, and Vite. Auth scaffolding comes from Laravel Breeze.

The README.md in the repo is the default GitLab template — ignore it.

## Commands

```bash
# PHP / backend
composer install
php artisan migrate                          # apply DB migrations
php artisan db:seed                          # seed (if seeders relevant)
php artisan serve                            # local dev server (default :8000; .env APP_URL uses :8098)
php artisan tinker                           # REPL
php artisan schedule:work                    # run scheduler locally (daily cash_back job)
php artisan queue:work                       # Redis queue worker (QUEUE_CONNECTION=redis)
./vendor/bin/pint                            # format PHP (Laravel Pint)

# Tests (PHPUnit 10, configured in phpunit.xml)
php artisan test                             # all tests
php artisan test --testsuite=Unit            # only unit tests
php artisan test --testsuite=Feature         # only feature tests
php artisan test --filter=SomeTestName       # single test by name
./vendor/bin/phpunit tests/Feature/Foo.php   # single file directly

# Frontend (Vite + Tailwind + Alpine)
npm install
npm run dev                                  # vite dev server (user runs manually)
npm run build                                # production build to public/build

# Custom artisan commands (in app/Console/Commands)
php artisan affiliate:run                    # AffiliateCommand
php artisan cashback:run                     # CashBackCommand
php artisan partner:commission               # PartnerCommissionCommand
```

The scheduler is wired in `app/Console/Kernel.php` to run `ManageMemberController@cash_back` daily.

## Environment

- DB: MySQL (`DB_DATABASE=ab` locally), sessions stored in DB (`SESSION_DRIVER=database`).
- Cache: file. Queue: Redis. Broadcast: Pusher.
- App listens on `127.0.0.1:8098`; the public-facing frontend runs on `127.0.0.1:8099` (`APP_FONTEND_URL`).
- `.env` contains live-looking secrets (PG Hard, BF agent, Telegram, API keys) — treat as sensitive even though committed locally; never echo their values back.
- `BF_AGENT`, `API_CAT`, `API_KEY` are used by callback/transaction integrations. `PGHARD_*` keys identify the operator for PG Hard.

## Architecture

### Routing layout
- `routes/web.php` — almost everything. The big `Route::middleware('auth')->group(...)` block is the admin panel; routes outside that group (at the bottom of the file) are unauthenticated cron/sync endpoints (`/sync_history`, `/get_cashback`, `/get_biggame`, `/pghard_report/...`, `/partner_call_winlose`, etc.) hit by external schedulers — be careful when changing them.
- `routes/api.php` — payment provider callbacks (`/jili`, `/jdb`, `/tf`), TrueMoney transfers (`/transfer_to_Bank`, `/transfer_to_Mobile`), SMS bank parsers (`/smsRequest`, `/smsSCB`, `/smsOTP`), and PaymentHub (`/PaymentGW`). These are unauthenticated by design; integrations rely on shared secrets/IPs.
- `routes/auth.php` — Breeze auth (login/register/password reset).

### Permission system
Custom middleware `CheckPermissionUser` in `app/Http/Middleware/CheckPermissionUser.php`, registered as alias `CheckPermissionUser`. Usage: `->middleware('CheckPermissionUser:member,view')`.

- Permissions live as a JSON column on `users.permissions` with keys `permitmember`, `permitmanageuser`, `permittransfer`, `permitreport`.
- Action levels: `hide=1`, `view=2`, `edit=3`, `delete=4`. Middleware fails (redirects `/`) when the stored level is **less than** the required action level.
- The function-name → permission-key mapping is hardcoded in the middleware. Adding a new permission domain requires editing both `$function_list` there and the user-permissions form.
- A second alias `RequireUserLevelZero` gates super-admin-only routes (e.g. admin login logs).
- `SetLocale` middleware reads `session('locale')` and sets `App::setLocale` per request — used by the language switcher (`/lang` route, `LanguageController`).

### Domain layout
Controllers in `app/Http/Controllers` are organized by feature, not REST resource. Notable groupings:
- **Member ops:** `ManageMemberController`, `TransactionController`, `BankAccountController`, `ManageUserController` (admin users, distinct from members).
- **Game integrations:** `CallbackController` (jili/jdb/tf), `HistoryController` (sync), `ProviderController`, `PgHardController`, `UfaTransactionReportController`, `UfaBillController`, plus AMB-specific: `AmbCategoryController`, `AmbProductController`, `AmbGameController`, `AmbHomepageItemController`.
- **Money rails:** `TMN_Controller` / `TMNOne` (TrueMoney), `PaymenthubController`, `SMSController` (bank-SMS-driven deposit confirmation).
- **Cross-cutting:** `SettingController` (large — owns popups, coupons, levels, ranking, mission, wheel, affiliate config, deposit/withdraw rules, maintenance, logo), `ReportController`, `PartnerController` (B2B partner/agent reporting), `PromotionController`, `PromotionAdsController`, `ArticleController` (CMS), `HashtagController`, `LogViewerController`, `AlertController`, `AdminLoginLogController`.
- `task.php` in the controllers directory is **not** a class — likely a script. Don't assume Laravel autoloads it.

### Models
Models in `app/Models` are mostly thin Eloquent wrappers over legacy DB tables. Many have unconventional names (`Bank_forward_balance`, `BigGameCallbaclk` [sic], `SAgaming`, `WmCallback`, `SexyCallback`, `Wrongdeposit`). When changing schemas, check migrations in `database/migrations` — most existing tables predate migrations and only the recent SBO/AMB/login-log changes are tracked there.

### Views
Blade templates under `resources/views`, grouped by feature (`amb/`, `manage-member/`, `transaction/`, `report/`, `setting/`, `partner/`, `provider/`, `promotion/`, `ads/`, `bank/`, `article/`, `links/`, `logs/`, `SMS/`, `admin/`, `auth/`, `components/`, `layouts/`). DataTables (yajra/laravel-datatables) is used heavily for admin grids — many index views are AJAX-fed by JSON endpoints in the same controllers.

### External integrations to be aware of
- **Telegram notifications** (`laravel-notification-channels/telegram`) — `TELEGRAM_BOT_TOKEN` / `TELEGRAM_G_ID`.
- **Hashids** (`vinkla/hashids`) — used to obfuscate IDs in URLs.
- **LINE notify** — endpoints like `/lineNotify_tranfer` plus `line_web_script.js` at the project root.
- **Pusher** for broadcasting.

## Conventions to respect

- The codebase mixes Thai-language comments and English. Don't translate or rewrite them.
- New game/provider integrations follow the existing pattern: a `*Callback` model, a method on `CallbackController` or a dedicated controller, and an unauthenticated `routes/api.php` entry.
- Routes that look duplicated at the bottom of `web.php` (`get_supergame` aliased to `get_SAhistory`, `get_Sexyhistory`, `get_WMhistory`) are intentional — older clients hit the old paths.
- AMB routes deliberately put literal paths (`/amb/categories`, `/amb/products`) **before** `/{id}` routes to avoid collisions; preserve that order when adding routes.
- When adding admin pages, add the matching `CheckPermissionUser:<domain>,<action>` middleware. Pages without it are accessible to any authenticated admin.
