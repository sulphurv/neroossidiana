<?php 

require_once __DIR__ . '/vendor/autoload.php';
// require __DIR__ . '/../bootstrap.php';

# Controllers
require "./controllers/home.controller.php";
require "./controllers/navbar.controller.php";
require "./controllers/products.controller.php";
require "./controllers/product-details.controller.php";
require "./controllers/login.controller.php";
require "./controllers/signup.controller.php";
require "./controllers/profile.controller.php";
require "./controllers/cart.controller.php";
require "./controllers/checkout.controller.php";
require "./controllers/payment.controller.php";
require "./controllers/footer.controller.php";
require "./controllers/admin.controller.php";

# Models
require "models/dbconfig.php";
require "models/my-products-repository.php";
require "models/navbar-repository.php";
require "models/checkout-repository.php";
require "models/account-repository.php";
require "models/admin-repository.php";
require "models/cart-line.php";
require "models/order-line.php";
require "models/view-models/product-details.model.php";
require "models/view-models/cart.model.php";
require "models/view-models/checkout.model.php";
require "models/view-models/home.model.php";
require 'models/view-models/admin-products.model.php';

# Infrastructure
require "./infrastructure/middleware.php";
require "./infrastructure/pagination.php";
require "./infrastructure/debugger.php";

?>