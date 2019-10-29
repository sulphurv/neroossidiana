<?php
namespace NeroOssidiana {

    use \Slim\Views\PhpRenderer as ViewRenderer;
    use NeroOssidiana\CheckoutRepository as CheckoutRepo;
    use NeroOssidiana\AccountRepository as AccountRepo;

    class CheckoutController
    {
        protected $view;
        private $repository;
        private $accountRepo;

        public function __construct(ViewRenderer $view, CheckoutRepo $repository, AccountRepo $accRepo)
        {
            $this->view = $view;
            $this->repository = $repository;
            $this->accountRepo = $accRepo;
        }

        public function __invoke($request, $response, $args)
        {
            if ($request->isPost()) {
                $formValues = $request->getParsedBody();
                
                if ($_SESSION["loggedIn"] && count($_SESSION["Customer"]) === 0) {
                    $this->accountRepo->insertAddress($formValues);
                }

                foreach ($formValues as $field => $value) {
                    $_SESSION["Customer"][$field] = $value;
                }

                header("Location: /Pagamento");
                exit;
            }

            if ($_SESSION["loggedIn"] && count($_SESSION["Customer"]) > 0) {
                header("Location: /Pagamento");
                exit;
            }

            $countries = file("./countries.txt");
            $response = $this->view->render($response, 'checkout.phtml', [
                "countries" => $countries,
                "total" => Cart::getTotal(),
                "shippingCosts" => CheckoutModel::getShippingCosts()
            ]);
            return $response;
        }
    }
}
