<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
$request = Request::createFromGlobals();

// Application switcher: if the route does not exist, launch legacy application.
$urlMatcherPath = __DIR__.'/../app/cache/dev/appDevUrlMatcher.php';
if (file_exists($urlMatcherPath)) {
    include $urlMatcherPath;

    $pathInfo = $request->getPathInfo();

    $context = new RequestContext();
    $context->fromRequest($request);
    $urlMatcher = new appDevUrlMatcher($context);
    try {
        $urlMatcher->match($pathInfo);
    } catch (ResourceNotFoundException $e) {
        // The requested route does not exist in the Symfony2 application.

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
        # RewriteRule (.+)\.confirmation\.html$ /app_dev.php?page=confirmation&id=$1

        include __DIR__.'/index.php'; // Launch the legacy application.
        exit;
    }
}

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
Request::enableHttpMethodParameterOverride();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
