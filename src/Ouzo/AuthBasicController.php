<?php
namespace Ouzo;

use Ouzo\Api\UnauthorizedException;
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
            throw new UnauthorizedException('Unauthorized user.', array('WWW-Authenticate: Basic realm="' . $this->realm . '"'));
        }
        return true;
    }
}