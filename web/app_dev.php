<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

// Preventing the debug front controller to be available on production servers.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$request = Request::createFromGlobals();

// Application switcher: if the route does not exist, launch legacy application.

// The cached URL matcher contains the routes of the Symfony2 application.
$urlMatcherPath = __DIR__.'/../app/cache/dev/appDevUrlMatcher.php';
if (!file_exists($urlMatcherPath)) { // If the URL matcher isn't already cached...
    $kernel->boot();
    $kernel->getContainer()->get('router')->getMatcher(); // ... Then generate it.
}
include $urlMatcherPath;

$pathInfo = $request->getPathInfo();

$context = new RequestContext();
$context->fromRequest($request);
$urlMatcher = new appDevUrlMatcher($context);
try {
    $requestAttributes = $urlMatcher->match($pathInfo);
} catch (ResourceNotFoundException $e) { // If the route does not exist in the Symfony2 application...
    // Converting routes.
    $routes = array(
        '/\/(.+)\.(confirmation)\.html/' => array(1 => 'id', 2 => 'page'),
        '/\/(.+)\.(envoi)\.html/' => array(1 => 'destinataire', 2 => 'page'),
        '/\/(.+)\.(bisous)\.html/' => array(1 => 'cancel', 2 => 'page'),
        '/\/(.+)\.(.+)\.(nuage)\.html/' => array(1 => 'sautnuage', 2 => 'sautposition', 3 => 'page'),
        '/\/(.+)\.(nuage)\.html/' => array(1 => 'nuage', 2 => 'page'),
        '/\/(.+)\.(.+)\.(action)\.html/' => array(1 => 'nuage', 2 => 'position', 3 => 'page'),
        '/\/(.+)\.(.+)\.(yeux)\.html/' => array(1 => 'Dnuage', 2 => 'Dpos', 3 => 'page'),
        '/\/(.+)\.(.+)\.(newpass)\.html/' => array(1 => 'Cid', 2 => 'Ccle', 3 => 'page'),
        '/\/(.+)\.(lire)\.html/' => array(1 => 'idmsg', 2 => 'page'),
        '/\/(livreor)\.(.+)\.html/' => array(1 => 'page', 2 => 'or'),
        '/\/(membres)\.(.+)\.html/' => array(1 => 'page', 2 => 'num'),
        '/\/(.+)\.html/' => array(1 => 'page'),
    );
    foreach ($routes as $expression => $parameters) {
        if (preg_match($expression, $pathInfo, $result)) {
            if (in_array('sautnuage', $parameters)) {
                $_GET['saut'] = 1;
            }
            foreach ($parameters as $index => $parameter) {
                $_GET[$parameter] = $result[$index];
            }
            break;
        }
    }
    include __DIR__.'/index.php'; // ... Then launch the legacy application.
    exit;
}

// Using the generated request attributes by the URL matcher.
// This will prevent the framework from calling the URL matcher again.
foreach ($requestAttributes as $key => $value) { 
    $request->attributes->set($key, $value);
}

// If the route does exist in the Symfony2 application, then launch it.
$kernel->loadClassCache();
Request::enableHttpMethodParameterOverride();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
