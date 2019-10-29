<?php 
namespace NeroOssidiana {
    use NeroOssidiana\AccountRepository as AccountRepo;

    class ProfileController
    {
        protected $view;
        private $repository;

        public function __construct($view, AccountRepo $repo)
        {
            $this->view = $view;
            $this->repository = $repo;
        }

        public function __invoke($request, $response, $args)
        {
            $this->repository->getUserData();

            $response = $this->view->render($response, "profile.phtml", [
                "user" => $_SESSION["User"],
                "customer" => $_SESSION["Customer"],
                "orders" => $_SESSION["CustomerOrders"]
            ]);
            
            return $response;
        }

        public function Settings($request, $response, $args)
        {
            # localStorage loggedin, da implementare
            /* if (!$_SESSION["User"]) {
                $this->repository->getUserData();
            } */

            if ($request->isGet()) {
                $arr = ["updated" => false, "error" => ""];
            }

            if ($request->isPost()) {
                $formValues = $request->getParsedBody();
                $result = $this->repository->updateProfile($formValues);

                if ($result["error"]) {
                    $arr = ["updated" => false, "error" => $result["error"]];
                } else {
                    $arr = ["updated" => true, "error" => ""];
                }
            }

            $response = $this->view->render($response, "profile-settings.phtml", $arr);
            return $response;
        }

        public function Address($request, $response, $args)
        {
            # localStorage loggedin, da implementare
            /* if (!$_SESSION["Customer"]) {
                $this->repository->getUserData();
            } */

            $countries = file("./countries.txt");

            if ($request->isGet()) {
                $arr  = ["countries" => $countries, "updated" => false];
            }

            if ($request->isPost()) {
                $formValues = $request->getParsedBody();
                $this->repository->insertAddress($formValues);
                $this->repository->getUserData();
                
                $arr  = ["countries" => $countries, "updated" => true];
            }

            $response = $this->view->render($response, "profile-address.phtml", $arr);
            return $response;
        }

        public function Orders($request, $response, $args)
        {
            /* if ($request->isPost()) {
                $formValues = $request->getParsedBody();
            } */

            # localStorage loggedin, da implementare
            /* if (!$_SESSION["CustomerOrders"]) {
                $this->repository->getUserData();
            } */
            
            $response = $this->view->render($response, "profile-orders.phtml", ["orders" => $_SESSION["CustomerOrders"]]);
            return $response;
        }

        public function Logout()
        {
            if ($_SESSION["loggedIn"]) {
                $this->repository->logout();
            }

            header("Location: /");
            exit;
        }

        public function Delete($request, $response, $args)
        {
            $formValues = $request->getParsedBody();
            $result = $this->repository->deleteProfile($formValues);

            if ($result["success"]) {
                $this->repository->logout();
                
                return $response->withRedirect("/");
            } else {
                $response = $this->view->render($response, "profile-settings.phtml", 
                ["updated" => false,
                 "error" => "La password inserita non è corretta"]);
            }
        }
    }
}

