# Project Context — Kaniz's Desi Style

সর্বশেষ পর্যালোচনা: 2026-11-06। ভবিষ্যতে কাজ শুরুর আগে এই নোট পড়ুন, তারপর সংশ্লিষ্ট সোর্স আবার যাচাই করুন। এটি source inspection থেকে তৈরি; পূর্ণ code audit বা চালু সাইটের যাচাই নয়।

## 1. প্রজেক্ট পরিচয়

Kaniz's Desi Style-এর Laravel backend, Vue storefront এবং Blade-ভিত্তিক admin/POS একই codebase-এ আছে। Catalog, ecommerce orders, inventory, purchases, sales, customer/supplier, accounts, reports, promotions ও site content-এর কোড রয়েছে। কিছু পুরোনো healthcare/pathology model, migration ও view-ও আছে; সেগুলোকে সক্রিয় ব্যবসায়িক feature ধরে নেওয়া যাবে না।

মূল [README](README.md)-তে marketing/feature description আছে; প্রতিটি দাবির বাস্তব implementation যাচাই করা হয়নি।

## 2. Technology ও dependencies

- Backend: PHP `^8.1`, Laravel `^10.10`; constraints দেখুন [composer.json](composer.json), exact locked versions দেখুন [composer.lock](composer.lock)।
- Frontend: Vue `^3.5.13`, Vue Router 4, Vuex 4, Axios, Vite 6, Tailwind 3; [package.json](package.json), [package-lock.json](package-lock.json)।
- UI: Bootstrap/jQuery/Owl Carousel theme assets-এর পাশাপাশি Vue components, Swiper, Fancyapps, toastification ইত্যাদি।
- Integrations: Sanctum, Spatie permissions, Yajra DataTables, DomPDF, Excel import/export, barcode, Intervention Image, bKash tokenize, Google API/analytics ও reCAPTCHA dependencies। Package থাকা মানেই integration configured/working নয়।
- Global PHP helpers Composer autoload করে: [helpers.php](app/Helper/helpers.php)।

## 3. কোথায় কী আছে

| কাজ | শুরু করার জায়গা |
| --- | --- |
| Backend web/admin routes | [web.php](routes/web.php) |
| JSON API routes | [api.php](routes/api.php) |
| Admin ecommerce/content | [ecommerce.php](routes/ecommerce.php) |
| Purchase / sales / promotions | [purchase.php](routes/purchase.php), [sales.php](routes/sales.php), [promotion.php](routes/promotion.php) |
| Admin authentication routes | [auth.php](routes/auth.php) |
| Admin controllers | [Controllers](app/Http/Controllers/) |
| Storefront API controllers | [API](app/Http/Controllers/API/) |
| Models / relationships | [Models](app/Models/) |
| Admin Blade screens/layouts | [views](resources/views/), [layouts](resources/views/layouts/) |
| Storefront HTML shell | [index.blade.php](resources/views/frontend/index.blade.php) |
| Vue startup / root / routing | [app.js](resources/js/app.js), [App.vue](resources/js/App.vue), [router.js](resources/js/router.js) |
| Storefront screens / shared UI | [views](resources/js/views/), [components](resources/js/components/) |
| Vuex state | [store](resources/js/store/), [modules](resources/js/store/modules/) |
| HTTP client | [axios.js](resources/js/axios.js) |
| CSS / legacy themes | [app.css](resources/css/app.css), [themes](themes/) |
| Database schema / manual changes | [migrations](database/migrations/), [alter.sql](alter.sql) |
| Uploaded media | [uploads](uploads/), [public/uploads](public/uploads/) |

## 4. Request ও authentication flow

1. [RouteServiceProvider](app/Providers/RouteServiceProvider.php) API routes-এ `/api` prefix এবং `api` middleware দেয়; web routes-এ `web` middleware দেয়। API limit 60 requests/minute per user/IP।
2. Admin routes `/admin` prefix-এর মধ্যে `auth`, `checkUserStatus`, `roleCheck:1,2,3` ব্যবহার করে। অনেক route-এ অতিরিক্ত `permission:*` আছে, তবে সব route-এ একই pattern নেই।
3. [Kernel](app/Http/Kernel.php)-এ Sanctum stateful middleware আছে। Protected API routes বর্তমানে `auth` ব্যবহার করে; `auth:sanctum` ধরে পরিবর্তন করবেন না। API group-এ throttling দুবার লেখা আছে।
4. [axios.js](resources/js/axios.js)-এ base URL `/` এবং `withCredentials = true`; storefront auth session/cookie-ভিত্তিক। [auth module](resources/js/store/modules/auth.js) `/api/login`, `/api/user`, `/api/logout` ইত্যাদি ডাকে।
5. [app.js](resources/js/app.js) auth user fetch শেষ করে Vue mount করে। Router guards checkout/account/wishlist-এর authentication দেখে।
6. Web routes-এর শেষ catch-all storefront shell render করে, `Setting` থেকে primary color পড়ে। তাই নতুন server route catch-all-এর আগে রাখতে হবে।
7. [CheckDatabaseConnection](app/Http/Middleware/CheckDatabaseConnection.php) connection fail হলে `my_sql_db_connection` named route-এ redirect করে; সংশ্লিষ্ট route declaration web file-এ commented দেখা গেছে।

## 5. Storefront ও data flow

- [router.js](resources/js/router.js): home, products/category slug, product details, dynamic pages, blog, gallery, login/register/password reset, compare, wishlist, checkout, success ও account subpages। Vue Router history mode ব্যবহার করে।
- Vuex modules: auth, home, product, product_detail, cart, order, wishlist, compare। UI/API contract বদলালে সংশ্লিষ্ট module এবং controller দুটো দেখুন।
- Catalog/home/filter/page API-এর প্রধান জায়গা [FrontendController](app/Http/Controllers/API/FrontendController.php)।
- Cart backend: [CartController](app/Http/Controllers/API/CartController.php), [CartService](app/Services/CartService.php), [Cart](app/Models/Cart.php), [CartItem](app/Models/CartItem.php)। Logged-in user-এর cart `user_id` দিয়ে, guest cart session UUID দিয়ে খোঁজে; guest expiry 30 দিন। Coupon session-এ থাকে।
- Wishlist/compare-এর নিজস্ব [services](app/Services/), [facades](app/Facades/) এবং [providers](app/Providers/) আছে।
- Ecommerce order flow: [API OrderController](app/Http/Controllers/API/OrderController.php) → [SalesOrder](app/Models/SalesOrder.php) ও [SalesOrderProduct](app/Models/SalesOrderProduct.php)। এখানে ecommerce `sale_type = 3`; manual SQL-এর পুরোনো comments-এ শুধু 1/2 আছে।
- Order creation-এ billing validation, customer linkage, transaction ও `ESO-` prefix আছে। Inventory/pricing/coupon/payment বদলানোর আগে সম্পূর্ণ order method ও সংশ্লিষ্ট admin sales flow পড়তে হবে।
- [Product](app/Models/Product.php) এবং [SalesOrder](app/Models/SalesOrder.php) soft deletes ব্যবহার করে। Product/inventory, customer, payments ও sister concern সম্পর্ক একাধিক module-এ প্রভাব ফেলে।
- bKash routes ও [BkashController](app/Http/Controllers/BkashController.php) আছে; checkout UI-তে method থাকা মানেই gateway settlement যাচাই হয়েছে নয়।

## 6. Build ও deployment-এর বিশেষ বিষয়

- [vite.config.js](vite.config.js)-এর entry: `resources/css/app.css`, `resources/js/app.js`; Laravel Vite plugin এবং Vue plugin ব্যবহৃত।
- Commands: `npm ci`, `npm run dev`, `npm run build`। এই সেশনে এগুলো চালানো হয়নি।
- একাধিক import-এ `@/` alias আছে, কিন্তু inspected Vite config-এ explicit alias নেই; build failure হলে প্রথমে alias resolution যাচাই করুন।
- এই backup-এ web entry [root index.php](index.php), root [.htaccess](.htaccess); `public/index.php` নেই। Root entry সরাসরি root-এর vendor/bootstrap লোড করে। সাধারণ Laravel `public/` document root বা `artisan serve` নির্দেশনা যাচাই ছাড়া ব্যবহার করবেন না।
- Root [build](build/) এবং [public/build](public/build/) দুটোই আছে। কোনটি serving হচ্ছে, manifest কোথায় যাচ্ছে এবং asset URL কী হচ্ছে যাচাই করে deploy করতে হবে। Generated bundle-এর বদলে source edit করুন।
- Root-এ [hot](hot) marker আছে; deployment-এ এটি Vite dev server reference হিসেবে ব্যবহৃত হচ্ছে কি না যাচাই করুন।
- Root এবং public-এর uploads directory দুটোই আছে; upload write path এবং browser asset path এক ধরে নেবেন না।

## 7. Local environment ও database readiness

এই সেশনে পর্যবেক্ষণ:

- PHP CLI 8.3.36 এবং Composer executable আছে।
- `node`, `npm`, `rg` PATH-এ পাওয়া যায়নি; search-এর জন্য `find`/`grep` ব্যবহার করতে হতে পারে।
- [vendor](vendor/) আছে; `node_modules` দেখা যায়নি। Dependency compatibility পরীক্ষা করা হয়নি।
- Root listing-এ `.env` পাওয়া যায়নি। [.env.example](.env.example) আছে; `.env1`-ও আছে, সেটির secret values পড়া বা এই নোটে কপি করা হয়নি। অনুমান করে backup credentials ব্যবহার করবেন না।
- `git status` ব্যর্থ: বর্তমান backup usable Git repository হিসেবে শনাক্ত হয়নি, যদিও `.git` directory দেখা যায়। Git history/diff availability ধরে নেবেন না।
- Database connection, app boot, browser behavior, build এবং tests এই documentation task-এ চালানো হয়নি।

পরবর্তীতে local setup-এর সময়:

1. PHP extensions ও locked Composer requirements যাচাই করুন; প্রয়োজন হলে `composer install`।
2. Local-only environment বানাতে [.env.example](.env.example) থেকে configuration নিন; database/mail/payment values আলাদাভাবে configure করুন। নতুন local environment হলে app key generate করুন; existing deployment-এর key বদলাবেন না।
3. Database dump/schema availability যাচাই করুন। [DatabaseSeeder](database/seeders/DatabaseSeeder.php) বর্তমানে কার্যত খালি; seed command admin/settings/reference data তৈরি করবে ধরে নেবেন না।
4. Migrations-এর পাশাপাশি [alter.sql](alter.sql)-এ manual schema changes আছে। Models-এর সঙ্গে clean migration schema-এর পূর্ণ সামঞ্জস্য যাচাই হয়নি। আগে disposable local DB-তে যাচাই করুন।
5. Document root/assets arrangement ঠিক করে তারপর app চালান।

## 8. Validation ও ভবিষ্যৎ কাজের নিয়ম

- [tests](tests/) মূলত Laravel/Breeze auth/profile ও example tests; পূর্ণ ecommerce/POS regression coverage নয়।
- [phpunit.xml](phpunit.xml)-এ testing env আছে, কিন্তু SQLite connection/database override commented। DB-changing tests-এর আগে dedicated test database নিশ্চিত করুন।
- Backend change: প্রয়োজনমতো PHP lint ও relevant `php artisan test`। Frontend change: toolchain প্রস্তুত হলে `npm run build`, তারপর সংশ্লিষ্ট browser flow যাচাই।
- Cart/order/inventory change হলে guest cart, login merge, quantity, discount/coupon, order creation ও stock/payment consistency-এর প্রভাব দেখুন।
- `copy` নামের legacy files আছে, যেমন [CartService copy.php](app/Services/CartService%20copy.php) ও [ProductsPage copy.vue](resources/js/views/ProductsPage%20copy.vue)। Import/route থেকে active file নিশ্চিত করুন।
- Public `/clear`, `/email` এবং bKash utility routes [web.php](routes/web.php)-এ দেখা গেছে; exposure/authorization ভবিষ্যৎ review-এর বিষয়, এই সেশনে পরিবর্তন করা হয়নি।
- Storefront shell-এ `noindex, follow` এবং computer-product সম্পর্কিত পুরোনো SEO copy আছে; SEO task হলে deployment intent ও brand content যাচাই করুন।
- Credentials, tokens, customer data এই নোট বা source documentation-এ রাখবেন না।
- কাজ শেষে এই নোটে নতুন নিশ্চিত তথ্য ও setup decisions update করুন।

## 9. এই সেশনের পরিবর্তন

শুধু এই context document তৈরি করা হয়েছে। Application code, configuration, database, credentials বা deployed site পরিবর্তন করা হয়নি।
