<?php 
namespace NeroOssidiana {
    use NeroOssidiana\AccountRepository as AccountRepo;

    class SignupController
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
            if ($_SESSION["loggedIn"]) {
                header("Location: /Profilo");
                exit;
            }

            if ($request->isGet()) {
                $arr = ["error" => ""];
            }

            if ($request->isPost()) {
                $formValues = $request->getParsedBody();

                $result = $this->repository->createAccount($formValues);

                if ($result["loggedIn"]) {
                    $_SESSION["loggedIn"] = true;
                    header("Location: /Profilo");
                    exit;
                } else {
                    $arr = ["error" => $result["error"]];
                }
            }

            $response = $this->view->render($response, "signup.phtml", $arr);
            return $response;
        }
    }
}


