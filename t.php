<?php
	require_once 'Loader.php';

	$l = new Loader();
// 'http://sk.advphp.labinfosys.pro/site/login', 'admin', 'password'

	// echo $l->get('http://sk.advphp.labinfosys.pro/');

	//var_dump($l->auth('http://sk.advphp.labinfosys.pro/site/login', 'admin', 'password'));

	echo $l->get('http://sk.advphp.labinfosys.pro/author/view?id=2');