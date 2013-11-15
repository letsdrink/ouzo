<?php
namespace Ouzo;

class HttpAuthBasicHandler
{
    public function authenticate($login, $password)
    {
        $realm = 'Authenticate';
        if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($realm) . '"');
            die('Authorization canceled.');
        }

        $this->_checkCredentials($login);

        $data = $this->_httpDigestParse($_SERVER['PHP_AUTH_DIGEST']);
        $A1 = md5($data['username'] . ':' . $realm . ':' . $password);
        $A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
        $validResponse = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);

        if ($data['response'] != $validResponse) {
            die('Wrong Credentials!');
        }
        return true;
    }

    private function _checkCredentials($login)
    {
        $data = $this->_httpDigestParse($_SERVER['PHP_AUTH_DIGEST']);
        if (!$data || $data['username'] == $login) {
            die('Wrong Credentials!');
        }
    }

    private function _httpDigestParse($authHeader)
    {
        $headerNeededParts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
        $data = array();
        $keys = implode('|', array_keys($headerNeededParts));
        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $authHeader, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $data[$match[1]] = $match[3] ? $match[3] : $match[4];
            unset($headerNeededParts[$match[1]]);
        }
        return $headerNeededParts ? false : $data;
    }
} 