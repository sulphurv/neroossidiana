<?php 

require "module.php";

use NeroOssidiana\DbConfig;

use NeroOssidiana\HomeController;
use NeroOssidiana\NavbarController;
use NeroOssidiana\ProductsController;
use NeroOssidiana\ProductDetailsController;
use NeroOssidiana\LoginController;
use NeroOssidiana\SignupController;
use NeroOssidiana\ProfileController;
use NeroOssidiana\CartController;
use NeroOssidiana\CheckoutController;
use NeroOssidiana\PaymentController;
use NeroOssidiana\FooterController;
use NeroOssidiana\AdminController;

use NeroOssidiana\NavbarRepository;
use NeroOssidiana\MyProductsRepository;
use NeroOssidiana\CheckoutRepository;
use NeroOssidiana\AccountRepository;
use NeroOssidiana\AdminRepository;

use Middleware\IsLoggedIn as isLoggedIn;
use Middleware\IsAdmin as isAdmin;

session_start();

if (!isset($_SESSION["Cart"])) {
    $_SESSION["Cart"] = [];
}
if (!isset($_SESSION["User"])) {
    $_SESSION["User"] = [];
}
if (!isset($_SESSION["Customer"])) {
    $_SESSION["Customer"] = [];
}
if (!isset($_SESSION["CustomerOrders"])) {
    $_SESSION["CustomerOrders"] = [];
}
if (!isset($_SESSION["loggedIn"])) {
    $_SESSION["loggedIn"] = false;
}
if (!isset($_SESSION["isAdmin"])) {
    $_SESSION["isAdmin"] = false;
}

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

// Initialize Slim
$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();

$container["db"] = function($c) {
    $conn = new PDO(DbConfig::dsn, DbConfig::username, DbConfig::password, []);
    # set the PDO error mode to exeption
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    # set how we want formatted the type of data fetched from the database 
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $conn;
};

$container["view"] = new \Slim\Views\PhpRenderer('./views/');

$container["paypalConfig"] = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        'ATJFY39-1J1TtwzUv0doqWkyrl0uzoGCd3Aq2Icb6Bmq75J8MkczInDLx3mkACF3KQrZHE4oGIFmM1to',
        'EFYcCb0u3xw3umyyCBOhrCywSXAEifpuAMfy8kGfRamzlon0YvRjy3xFOrksd6803w3ZyjwefMWyWIA0'
    )
);

$container["NavbarRepo"] = function($c) {
    $db = $c->get("db");
    return new NavbarRepository($db);
};
$container["ProductsRepo"] = function($c) {
    $db = $c->get("db");
    return new MyProductsRepository($db);
};
$container["CheckoutRepo"] = function($c) {
    $db = $c->get("db");
    return new CheckoutRepository($db);
};
$container["AccountRepo"] = function($c) {
    $db = $c->get("db");
    return new AccountRepository($db);
};
$container["AdminRepo"] = function($c) {
    $db = $c->get("db");
    return new AdminRepository($db);
};

$container["HomeController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("ProductsRepo");
    return new HomeController($view, $repository);
};
$container["NavbarController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("NavbarRepo");
    return new NavbarController($view, $repository);
};
$container["ProductsController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("ProductsRepo");
    return new ProductsController($view, $repository);
};
$container["ProductDetailsController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("ProductsRepo");
    return new ProductDetailsController($view, $repository);
};
$container["LoginController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("AccountRepo");
    return new LoginController($view, $repository);
};
$container["SignupController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("AccountRepo");
    return new SignupController($view, $repository);
};
$container["ProfileController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("AccountRepo");
    return new ProfileController($view, $repository);
};
$container["CartController"] = function($c) {
    $view = $c->get("view");
    $repository = $c->get("ProductsRepo");
    return new CartController($view, $repository);
};
$container["CheckoutController"] = function($c) {
    $view = $c->get("view");
    $checkoutRepository = $c->get("CheckoutRepo");
    $accountRepository = $c->get("AccountRepo");
    return new CheckoutController($view, $checkoutRepository, $accountRepository);
};
$container["PaymentController"] = function($c) {
    $view = $c->get("view");
    $paypalConfig = $c->get("paypalConfig");
    $checkoutRepository = $c->get("CheckoutRepo");
    return new PaymentController($view, $checkoutRepository, $paypalConfig);
};
$container["FooterController"] = function($c) {
    $view = $c->get("view");
    return new FooterController($view);
};
$container["AdminController"] = function($c) {
    $view = $c->get("view");
    $adminRepository = $c->get("AdminRepo");
    return new AdminController($view, $adminRepository);
};

// Define a route and anonymous function to serve as a controller
$app->get('/', "HomeController");

$app->get("/Navbar-data", "NavbarController");

$app->get("/Cerca/{Prodotto}", "NavbarController:GetHint");

$app->post("/Cerca", "ProductsController:Search");

$app->get("/Prodotti/{Gender}/{Category}/{Type}/Page{Number:[0-9]+}", "ProductsController");

$app->get("/Prodotti/{Gender}/{Category}/Page{Number:[0-9]+}", "ProductsController");

$app->get("/Prodotti/{Gender}/Page{Number:[0-9]+}", "ProductsController");

$app->map(["GET", "POST"], "/Dettagli-Prodotto/{ProductID}", "ProductDetailsController");

$app->map(["GET", "POST"], "/Login", "LoginController");

$app->map(["GET", "POST"], "/Crea-Profilo", "SignupController");

$app->get("/Profilo", "ProfileController")->add(new isLoggedIn());

$app->map(["GET", "POST"], "/Profilo/Impostazioni", "ProfileController:Settings")->add(new isLoggedIn());

$app->map(["GET", "POST"], "/Profilo/Indirizzi", "ProfileController:Address")->add(new isLoggedIn());

$app->map(["GET", "POST"], "/Profilo/Ordini", "ProfileController:Orders")->add(new isLoggedIn());

$app->get("/Profilo/Logout", "ProfileController:Logout")->add(new isLoggedIn());

$app->post("/Profilo/Cancella", "ProfileController:Delete")->add(new isLoggedIn());

$app->map(["GET", "POST"], "/Carrello", "CartController");

$app->map(["GET", "POST"], "/Checkout", "CheckoutController");

$app->get("/Pagamento", "PaymentController");

$app->post("/Pagamento/paypal", "PaymentController:PaypalPay");

$app->get("/Pagamento/paypal/process", "PaymentController:PaypalProcess");

$app->get("/Pagamento/paypal/cancel", "PaymentController:PaypalCancel");

$app->get("/Grazie", "PaymentController:Confirm");

$app->get("/Contattaci", "FooterController:ContactUs");

$app->get("/Supporto/{Section}", "FooterController:Support");

$app->get("/Chi-Siamo", "FooterController:AboutUs");

$app->map(["GET", "POST"], "/Admin", "AdminController");

$app->get("/Admin/Ordini/{Action}", "AdminController:Orders")->add(new isAdmin());

$app->map(["GET", "POST"], "/Admin/Ordini", "AdminController:Orders")->add(new isAdmin());

$app->map(["GET", "POST"], "/Admin/Dettagli-ordine/{OrderID}", "AdminController:OrderDetails")->add(new isAdmin());

$app->get("/Admin/Prodotti/{Action}/", "AdminController:Products")->add(new isAdmin());

$app->post("/Admin/Prodotti/{Action}", "AdminController:Products")->add(new isAdmin());

$app->map(["GET", "POST"], "/Admin/Prodotti", "AdminController:Products")->add(new isAdmin());

$app->get("/Admin/Prodotti/Suggerimento/{Keyword}", "AdminController:GetHint")->add(new isAdmin());

$app->post("/Admin/Gestione-prodotto/Fetch-Data", "AdminController:FetchData")->add(new isAdmin());

$app->map(["GET", "POST"], "/Admin/Gestione-prodotto/{Action}/{ProductID}", "AdminController:ManageProducts")->add(new isAdmin());

$app->map(["GET", "POST"], "/Admin/Gestione-prodotto/{Action}", "AdminController:ManageProducts")->add(new isAdmin());

$app->run();