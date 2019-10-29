<?php
namespace NeroOssidiana {

    use \Slim\Views\PhpRenderer as ViewRenderer;

    class FooterController
    {
        protected $view;

        public function __construct(ViewRenderer $vw)
        {
            $this->view = $vw;
        }

        public function ContactUs($request, $response, $args)
        {
            $this->view->render($response, "contact-us.phtml", []);

            return $response;
        }

        public function Support($request, $response, $args)
        {
            switch($args["Section"]) {
                case "FAQ":
                    $data = file("./faq.txt");
                    break;
                case "Taglie":
                    $data = ["-h1 Taglie"];
                    break;
                case "Consegna":
                    $data = ["-h1 Consegna"];
                    break;
                case "Politica-sui-resi":
                    $data = ["-h1 Politica sui resi"];
                    break;
                case "Informativa-sulla-privacy":
                    $data = ["-h1 Informativa sulla privacy"];
                    break;
                case "Diritto-di-recesso":
                    $data = ["-h1 Diritto di recesso"];
                    break;
                case "Termini-e-condizioni":
                    $data = ["-h1 Termini e condizioni"];
                    break;
                case "Promozioni-e-offerte":
                    $data = ["-h1 Promozioni e offerte"];
                    break;
                default:
                    return $response->withRedirect("/page-not-found");
            }

            $this->view->render($response, "support.phtml", ["data" => $data]);

            return $response;
        }

        public function AboutUs($request, $response, $args)
        {
            $this->view->render($response, "about-us.phtml", []);

            return $response;
        }
    }
}
