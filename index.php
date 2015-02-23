<?php
session_cache_limiter(false);
session_start();

require 'vendor/autoload.php';

// "USE" STATEMENTS
// use App\Example;

// APP CONFIG
$app = new \Slim\Slim(array(
	// TODO : Put debug to false when deploying to prod.
    'debug' => true,
    'mode' => 'dev',//dev or prod

    'log.enabled' => true,
    'log.path' => 'logs',
    'log.level' => 8,
    'log.writer' => new \Slim\Logger\DateTimeFileWriter(),

    'templates.path' => 'templates',
    'cookies.encrypt' => true,
    'cookies.secure' => false,  // Works only over HTTPS.
    'cookies.httponly' => true,
    'cookies.secret_key' => 'CHANGEME',
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC,
    'cookies.lifetime' => '2 days',

    'view' => new \Slim\Views\Twig()
));

// GLOBAL MIDDLEWARES
// $app->add(new \App\CustomErrorMiddleware());

// HOOKS
// $app->hook('slim.before', function () use ($app) { /* ... */ });

// Adds the page variable into template to use as a CSS class for page-specific style.
$app->hook('slim.before.dispatch', function () use ($app){
    $pattern = $app->router()->getCurrentRoute()->getPattern();
    $page = preg_replace('/\//', '-', $pattern);
    $page = preg_replace('/(\:([A-Za-z0-9])*)/', '', $page);
    $app->view->setData('page', $page);
});

// ERROR HANDLING
$app->notFound(function () use ($app) {
    $app->render('http/404.html');
});
$app->error(function (\Exception $e) use ($app) {
    $app->render('http/500.html', array('message' => $e->getMessage()));
});

// TWIG CONFIG
$view = $app->view();
$view->parserDirectory = 'vendor/twig/twig';
$view->parserOptions = array(
	// TODO : Put debug to false when deploying to prod.
    'debug' => true,
    'cache' => dirname(__FILE__) . '/cache'
);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new Twig_Extension_Debug(),
    //new \App\TwigExtensionCustom(),
);


// ROUTES
$app->get('/:name', function($name) use ($app){
	$app->render('home.html', array('name' => $name));
})->name('home')->conditions(array('name' => '(\w)*'));

// BOOT
$app->run();