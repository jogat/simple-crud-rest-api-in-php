<?php

require_once "VistaApi.php";

/**
 * Class to output output responses with JSON format
 */
class VistaJson extends VistaApi
{
    
    public function __construct($estado = 400)
    {
        $this->estado = $estado;
    }

    /**
     * Prints the body of the response and sets the response code
     * @param mixed $cuerpo body of the response to send
     */
    public function imprimir($cuerpo)
    {
        
        http_response_code($cuerpo['estado']); 
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($cuerpo, JSON_PRETTY_PRINT);
        exit;
    }
}