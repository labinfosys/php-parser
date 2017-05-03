<?php
class Loader 
{
    public $useragent = 'Opera/10.00 (Windows NT 5.1; U; ru) Presto/2.2.0';
    private $ch = null;

    public function __construct()
    {
        // $this->login = $user;
        // $this->password = $password;
        // $this->url = $url;
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);

        curl_setopt($this->ch, CURLOPT_COOKIEJAR,  __DIR__ . '/cookie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    }

    public function get($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
        if (curl_errno($this->ch)) {
            return false;
        }
        return $result;
    }

    public function post($url, $data)
    {
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($this->ch);
        if (curl_errno($this->ch)) {
            return false;
        }
        return $result;
    }

    public function auth($url, $user, $password) 
    {
        $responce = $this->get($url);
        if (self::isLoggedIn($responce))
            return true;

        $csrf_param = self::getMetaInfo($responce, 'csrf-param');
        $csrf_token = self::getMetaInfo($responce, 'csrf-token');
        $post = [
            'LoginForm[username]' => $user,
            'LoginForm[password]'  => $password,
            $csrf_param => $csrf_token
        ];

        $responce = $this->post($url, $post);

        return self::isLoggedIn($responce);
    }

    static public function getMetaInfo($str, $metaName) 
    {
        $pattern = '/meta\s+name="' . $metaName . '"\s+content="([\w=]+)"/i';
        $matches = [];
        preg_match($pattern, $str, $matches);
        if (count($matches) > 1)
            return $matches[1];
        return '';
    }

    static public function isLoggedIn($str)
    {
        return strpos($str, 'Logout (admin)') > 0;
    }
}
