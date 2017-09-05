<?php

abstract class VistaApi{
    
    // Error code
    public $estado;

    public abstract function imprimir($cuerpo);
}