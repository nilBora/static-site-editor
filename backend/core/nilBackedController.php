<?php

class nilBackendController
{
    public $auth = false;

    public function __construct()
    {
        session_start();
    }

    public function auth($login, $password)
    {
        if(substr($_SERVER['REQUEST_URI'], 0, 9)=='/backend/')
        {
            $allow = false;

            if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
            {
                if ($_SERVER['PHP_AUTH_USER'] == $login && $_SERVER['PHP_AUTH_PW'] == $password)

                    $allow = true;
            }

            if (!$allow)
            {
                unset($_SESSION['nilAuthUser']);
                header('WWW-Authenticate: Basic realm="Please login"');
                header('HTTP/1.0 401 Unauthorized');

                echo "Access denied!";
                exit;
            }

            $_SESSION['nilAuthUser'] = 1;
            $this->auth = $allow;
            return $this->auth;
        }

    }


}