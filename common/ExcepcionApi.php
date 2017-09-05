<?php

class ExcepcionApi extends Exception
{
    public $estado;    

    public function ExcepcionApi($estado, $mensaje, $codigo = 400)
    {
        $this->estado = $estado;
        $this->message = $mensaje;
        $this->code = $codigo;
    }

}

?>