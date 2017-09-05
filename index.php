<?php
require 'vistas/VistaXML.php';
require 'vistas/VistaJson.php';
require 'common/ExcepcionApi.php';

$directory = 'controllers';
$controllers = array();
$path  = array_diff(scandir($directory), array('..', '.'));
foreach ($path as $key => $value) {
    $controllers[] = str_replace('.php','',$value);
    require $directory.'/'.$value;
}


// determines the output format
$formato = isset($_GET['format']) ? $_GET['format'] : 'json';

switch ($formato) {
    case 'xml':
        $view = new VistaXML();
        break;
    case 'json':
        $view = new VistaJson();
    default:
        $view = new VistaJson();
}

// Prepare exception handling
set_exception_handler(function ($exception) use ($view) {
    $cuerpo = array(        
        "estado" => $view->estado,
        "mensaje" => $exception->getMessage()
    );
    if ($exception->getCode()) {
        $view->estado = $exception->getCode();
    } else {
        $view->estado = 500;
    }

    $view->imprimir($cuerpo);
}
);



// Extract segment from url
if (isset($_GET['PATH_INFO']))
    $fullRequest = explode('/', $_GET['PATH_INFO']);
else
    throw new ExcepcionApi(400, utf8_encode("The request is not recognized"));


// Extract controller
$controller = array_shift($fullRequest);


// Verify if controller exists 
if (!in_array($controller, $controllers)) {
    throw new ExcepcionApi(400,"The resource you are trying to access is not recognized");
}

// Verify the method
$method = strtolower($_SERVER['REQUEST_METHOD']);

// Verify if method exists in controller
if (method_exists($controller, $method)) {
    $currentController = new $controller;
    $response = call_user_func(array($currentController,$method));
    $view->imprimir($response);    
}
else{
    $view->estado = 405;
    $cuerpo = [
        "estado" => 405,
        "mensaje" => utf8_encode("Not allowed method")
    ];
    $view->imprimir($cuerpo);
}


?>