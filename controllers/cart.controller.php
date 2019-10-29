<?php
namespace NeroOssidiana {

    use NeroOssidiana\MyProductsRepository as ProductsRepo;
    use \Slim\Views\PhpRenderer as ViewRenderer;

    class CartController
    {

        protected $view;
        private $repository; 

        public function __construct(ViewRenderer $view, ProductsRepo $repository)
        {
            $this->view = $view;
            $this->repository = $repository;
        }

        public function __invoke($request, $response, $args)
        {
            if (count($_SESSION["Cart"]) > 0) { 
                $viewArr = [
                    "total" => Cart::getTotal(),
                    "deliveryDate" => CartModel::getDeliveryDate(),
                    "shippingCosts" => CheckoutModel::getShippingCosts()
                ];
            } else {
                $viewArr = [];
            }

            if ($request->isPost()) {
                $formValues = $request->getParsedBody();

                if (isset($formValues["_method"])) {

                    if ($formValues["_method"] == "PATCH") {
                        Cart::updateQuantity($formValues);
                        header("Location: /Carrello");
                        exit;
                    }

                    if ($formValues["_method"] == "DELETE") {
                        Cart::removeItem($formValues["index"]);
                        header("Location: /Carrello");
                        exit;
                    }
                }
            }

            $response = $this->view->render($response, 'cart.phtml', $viewArr);
            return $response;
        }
    }
}
