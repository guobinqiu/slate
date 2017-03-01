<?php

use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
$office_ip_addresses = [
  '180.168.105.42',    # China office
  '123.1.191.42',      # HK VPN
  '158.199.142.139',   # JP VPN
  '153.121.52.149',    # JP VPN
  '192.168.33.1',      # vagrant
  '127.0.0.1',         # localhost
  '::1'                # localhost
];

 if (isset($_SERVER['HTTP_CLIENT_IP'])
     || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
     || (!in_array(@$_SERVER['REMOTE_ADDR'], $office_ip_addresses) && substr($_SERVER['REMOTE_ADDR'], 0, 10) != '192.168.1.')
 ) {
     header('HTTP/1.0 403 Forbidden');
     exit('你无权访问此页面.');
 }

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
