<?php
namespace Ouzo;

use Ouzo\Api\UnauthorizedException;
use Ouzo\ExceptionHandling\Error;
use Ouzo\Utilities\Arrays;

class AuthBasicController extends Controller
{
    public $login;
    public $password;
    public $realm;

    public function httpAuthBasic($login, $password, $realm = 'Ouzo Auth')
    {
        $this->login = $login;
        $this->password = $password;
        $this->realm = $realm;
        $this->before[] = 'authorize';
    }

    public function authorize()
    {
        $login = Arrays::getValue($_SERVER, 'PHP_AUTH_USER');
        $pass = Arrays::getValue($_SERVER, 'PHP_AUTH_PW');
        if ($this->login != $login || $this->password != $pass) {
            $code = defined('UNAUTHORIZED') ? UNAUTHORIZED : 0;
            $error = new Error($code, I18n::t('exception.unauthorized'));
            throw new UnauthorizedException($error, array('WWW-Authenticate: Basic realm="' . $this->realm . '"'));
        }
        return true;
    }
}