<?php

require_once "VistaApi.php";

/**
 * Class to output output responses in XML format
 */
class VistaXML extends VistaApi
{

    /**
     * Prints the body of the response and sets the response code
     * @param mixed $cuerpo of the response to send
     */
    public function imprimir($cuerpo)
    {
        if ($this->estado) {
            http_response_code($this->estado);
        }

        header('Content-Type: text/xml');

        $xml = new SimpleXMLElement('<respuesta/>');
        self::parsearArreglo($cuerpo, $xml);
        print $xml->asXML();

        exit;
    }

    /**
     * Converts an array to XML
     * @param array $data array to convert
     * @param SimpleXMLElement $xml_data root element
     */
    public function parsearArreglo($data, &$xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item' . $key;
                }
                $subnode = $xml_data->addChild($key);
                self::parsearArreglo($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}