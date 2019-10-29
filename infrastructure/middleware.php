<?php
namespace Middleware {

    class IsLoggedIn
    {
        public function __invoke($request, $response, $next)
        {
            if ($_SESSION["loggedIn"]) {
                $response = $next($request, $response);

                return $response;
            } else {
               return $response->withRedirect("/");
            }
        }
    }

    class IsAdmin
    {
        public function __invoke($request, $response, $next)
        {
            if ($_SESSION["isAdmin"]) {
                $response = $next($request, $response);

                return $response;
            } else {
               return $response->withRedirect("/");
            }
        }
    }
}
