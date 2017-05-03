<?php

function getMetaInfo($str, $metaName) {
    $pattern = '/meta\s+name="' . $metaName . '"\s+content="([\w=]+)"/i';
    $matches = [];
    preg_match($pattern, $str, $matches);
    if (count($matches) > 1)
        return $matches[1];
    return '';
}

function isLoggedIn($str)
{
    return strpos($str, 'Logout (admin)') > 0;
}

function auth() {
    $useragent = 'Opera/10.00 (Windows NT 5.1; U; ru) Presto/2.2.0';
    $login = 'admin';
    $password = 'password';
    $url = 'http://sk.advphp.labinfosys.pro/site/login';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    curl_setopt($ch, CURLOPT_COOKIEJAR,  __DIR__ . '/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $r = curl_exec($ch);

    if (isLoggedIn($r)) return true;

    if (curl_errno($ch)) {
        echo curl_error($ch) . "\n";
        return false;
    }

    $csrf_param = getMetaInfo($r, 'csrf-param');
    $csrf_token = getMetaInfo($r, 'csrf-token');

    echo $csrf_param . "\n";
    echo $csrf_token . "\n";

    $post = [
        'LoginForm[username]' => $login,
        'LoginForm[password]'  => $password,
        $csrf_param => $csrf_token
    ];

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    $result = curl_exec($ch);
    return isLoggedIn($result);
}

// $l = load('http://exist.ru');
//echo strpos($l, 'Войти');

$a = auth();
var_dump($a);
// echo strpos($l, 'Войти');
// echo load('http://exist.ru');