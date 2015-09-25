<?php

/**
 * Description of Pasarela
 *
 * @author Josmel
 */
class App_Controller_Action_Helper_PasarelaVisa extends Zend_Controller_Action_Helper_Abstract {

    public function __construct() {
        $this->_config = Zend_Registry::get('config');
        $this->_formularioVisa = $this->_config['pasarela']['visa']['formulario'];
        $this->_codigoTienda = $this->_config['pasarela']['visa']['codigotienda'];
    }

    public function generarTiket($arrDatos) {
        $parametros = array();
        $parametros['xmlIn'] = $this->nuevoTicket($arrDatos);
        $this->_clienteSoapGenerarTiket = new Zend_Soap_Client(
                $this->_config['pasarela']['visa']['generartiket'], array('soap_version' => SOAP_1_1));
        $result = $this->_clienteSoapGenerarTiket->GeneraEticket($parametros);
        $xmlDocument = new DOMDocument();
        if ($xmlDocument->loadXML($result->GeneraEticketResult)) {
            /////////////////////////[MENSAJES]////////////////////////
            $iCantMensajes = $this->CantidadMensajes($xmlDocument);
            for ($iNumMensaje = 0; $iNumMensaje < $iCantMensajes; $iNumMensaje++) {
                echo 'Mensaje #' . ($iNumMensaje + 1) . ': ';
                echo $this->RecuperaMensaje($xmlDocument, $iNumMensaje + 1);
                echo '<BR>';
                echo "Numero de pedido: " . $arrDatos[0];
            }
            /////////////////////////[MENSAJES]////////////////////////
            if ($iCantMensajes == 0) {
                $Eticket = $this->RecuperaEticket($xmlDocument);
                $result = $this->htmlRedirecFormEticket($Eticket);
                echo $result;
            }
        } else {
            return "Error cargando XML";
        }
    }

    public function consultaEticket($eTicket) {
        $parametros = array();
        $parametros['xmlIn'] = $this->consultaTiketXml($this->_codigoTienda, $eTicket);
        $this->_clienteSoapConsultaTiket = new Zend_Soap_Client(
                $this->_config['pasarela']['visa']['consultatiket'], array('soap_version' => SOAP_1_1));
        $result = $this->_clienteSoapConsultaTiket->ConsultaEticket($parametros);
        $xmlDocument = new DOMDocument();
        if ($xmlDocument->loadXML($result->ConsultaEticketResult)) {
            $iCantOpe = $this->CantidadOperaciones($xmlDocument, $eTicket);
            $HTML['cantidad_operaciones'] = $iCantOpe;
            for ($iNumOperacion = 0; $iNumOperacion < $iCantOpe; $iNumOperacion++) {
                $HTML['resultado'] = $this->PresentaResultado($xmlDocument, $iNumOperacion + 1);
            }
            $iCantMensajes = $this->CantidadMensajes($xmlDocument);
            $HTML['cantidad_mensajes'] = $iCantMensajes;
            for ($iNumMensaje = 0; $iNumMensaje < $iCantMensajes; $iNumMensaje++) {
                $HTML['mensaje'] = 'Mensaje #' . ($iNumMensaje + 1) . ': ' . $this->RecuperaMensaje($xmlDocument, $iNumMensaje + 1);
            }
            return $HTML;
        } else {
            $HTML = "Error";
            return $HTML;
        }
    }

    public function nuevoTicket($arrDatosFinish) {
        $xmlIn = "";
        $xmlIn = $xmlIn . "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        $xmlIn = $xmlIn . "<nuevo_eticket>";
        $xmlIn = $xmlIn . "	<parametros>";
        $xmlIn = $xmlIn . "		<parametro id=\"CANAL\">3</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"PRODUCTO\">1</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"CODTIENDA\">" . $this->_codigoTienda . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"NUMORDEN\">" . $arrDatosFinish[0] . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"MOUNT\">" . $arrDatosFinish[1] . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"NOMBRE\">" . $arrDatosFinish[2] . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"APELLIDO\">" . $arrDatosFinish[3] . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"CIUDAD\">" . $arrDatosFinish[4] . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"DIRECCION\">" . $arrDatosFinish[5] . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"CORREO\">" . $arrDatosFinish[6] . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"DATO_COMERCIO\">" . $arrDatosFinish[7] . "</parametro>";
        $xmlIn = $xmlIn . "	</parametros>";
        $xmlIn = $xmlIn . "</nuevo_eticket>";
        return $xmlIn;
    }

    protected function consultaTiketXml($codTienda, $eTicket) {
        $xmlIn = "";
        $xmlIn = $xmlIn . "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        $xmlIn = $xmlIn . "<consulta_eticket>";
        $xmlIn = $xmlIn . "	<parametros>";
        $xmlIn = $xmlIn . "		<parametro id=\"CODTIENDA\">";
        $xmlIn = $xmlIn . $codTienda; //Aqui se asigna el Código de tienda
        $xmlIn = $xmlIn . "</parametro>";
        $xmlIn = $xmlIn . "		<parametro id=\"ETICKET\">";
        $xmlIn = $xmlIn . $eTicket; //Aqui se asigna el eTicket
        $xmlIn = $xmlIn . "</parametro>";
        $xmlIn = $xmlIn . "	</parametros>";
        $xmlIn = $xmlIn . "</consulta_eticket>";
        return $xmlIn;
    }

    //Funcion que genera Numero de Pedido
    function NumPedido() {
        $archivo = $this->_config['app']['numeroPedido'];
        $numPedido = 0;

        $fp = fopen($archivo, "r");
        $numPedido = fgets($fp, 26);
        fclose($fp);

        ++$numPedido;

        $fp = fopen($archivo, "w+");
        fwrite($fp, $numPedido, 26);
        fclose($fp);

        return $numPedido;
    }

    function RecuperaCampos($xmlDoc, $sNumOperacion, $nomCampo) {
        $strReturn = "";

        $xpath = new DOMXPath($xmlDoc);
        $nodeList = $xpath->query("//operacion[@id='" . $sNumOperacion . "']/campo[@id='" . $nomCampo . "']");

        $XmlNode = $nodeList->item(0);

        if ($XmlNode == null) {
            $strReturn = "";
        } else {
            $strReturn = $XmlNode->nodeValue;
        }
        return $strReturn;
    }

    function PresentaResultado($xmlDoc, $iNumOperacion) {
        $sNumOperacion = "";
        $sNumOperacion = $iNumOperacion;
        $strValor[$sNumOperacion]['Respuesta'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "respuesta");
        $strValor[$sNumOperacion]['Estado'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "estado");
        $strValor[$sNumOperacion]['Cod_tienda'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "cod_tienda");
        $strValor[$sNumOperacion]['Nordent'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "nordent");
        $strValor[$sNumOperacion]['Cod_accion'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "cod_accion");
        $strValor[$sNumOperacion]['Pan'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "pan");
        $strValor[$sNumOperacion]['Nombre_th'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "nombre_th");
        $strValor[$sNumOperacion]['Ori_tarjeta'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "ori_tarjeta");
        $strValor[$sNumOperacion]['Nom_emisor'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "nom_emisor");
        $strValor[$sNumOperacion]['ECI'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "eci");
        $strValor[$sNumOperacion]['Dsc_ECI'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "dsc_eci");
        $strValor[$sNumOperacion]['Cod_autoriza'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "cod_autoriza");
        $strValor[$sNumOperacion]['Cod_rescvv2'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "cod_rescvv2");
        $strValor[$sNumOperacion]['ID_UNICO'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "id_unico");
        $strValor[$sNumOperacion]['Imp_autorizado'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "imp_autorizado");
        $strValor[$sNumOperacion]['Fechayhora_tx'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "fechayhora_tx");
        $strValor[$sNumOperacion]['Fechayhora_deposito'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "fechayhora_deposito");
        $strValor[$sNumOperacion]['Fechayhora_devolucion'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "fechayhora_devolucion");
        $strValor[$sNumOperacion]['Dato_comercio'] = $this->RecuperaCampos($xmlDoc, $sNumOperacion, "dato_comercio");
        return $strValor;
    }

//Funcion de ejemplo que obtiene la cantidad de operaciones
    function CantidadOperaciones($xmlDoc, $eTicket) {
        $cantidaOpe = 0;
        $xpath = new DOMXPath($xmlDoc);
        $nodeList = $xpath->query('//pedido[@eticket="' . $eTicket . '"]', $xmlDoc);

        $XmlNode = $nodeList->item(0);

        if ($XmlNode == null) {
            $cantidaOpe = 0;
        } else {
            $cantidaOpe = $XmlNode->childNodes->length;
        }
        return $cantidaOpe;
    }

    //Funcion  que obtiene la cantidad de mensajes
    function CantidadMensajes($xmlDoc) {
        $cantMensajes = 0;
        $xpath = new DOMXPath($xmlDoc);
        $nodeList = $xpath->query('//mensajes', $xmlDoc);

        $XmlNode = $nodeList->item(0);

        if ($XmlNode == null) {
            $cantMensajes = 0;
        } else {
            $cantMensajes = $XmlNode->childNodes->length;
        }
        return $cantMensajes;
    }

    //Funcion que recupera el valor de uno de los mensajes XML de respuesta
    function RecuperaMensaje($xmlDoc, $iNumMensaje) {
        $strReturn = "";

        $xpath = new DOMXPath($xmlDoc);
        $nodeList = $xpath->query("//mensajes/mensaje[@id='" . $iNumMensaje . "']");

        $XmlNode = $nodeList->item(0);

        if ($XmlNode == null) {
            $strReturn = "";
        } else {
            $strReturn = $XmlNode->nodeValue;
        }
        return $strReturn;
    }

    //Funcion que recupera el valor del Eticket
    function RecuperaEticket($xmlDoc) {
        $strReturn = "";

        $xpath = new DOMXPath($xmlDoc);
        $nodeList = $xpath->query("//registro/campo[@id='ETICKET']");

        $XmlNode = $nodeList->item(0);

        if ($XmlNode == null) {
            $strReturn = "";
        } else {
            $strReturn = $XmlNode->nodeValue;
        }
        return $strReturn;
    }

    function htmlRedirecFormAnt($CODTIENDA, $NUMORDEN, $MOUNT) {
        $html = '<Html>
	<head>
	<title>Pagina prueba Visa</title>
	</head>
	<Body onload="fm.submit();">

	<form name="fm" method="post" action="' . $this->_formularioVisa . '">
	    <input type="hidden" name="CODTIENDA" value="#CODTIENDA#" /><BR>
	    <input type="hidden" name="NUMORDEN" value="#NUMORDEN#" /><BR>
	    <input type="hidden" name="MOUNT" value="#MOUNT#" /><BR>
	</form>
	</Body>
	</Html>';

        $html = ereg_replace("#CODTIENDA#", $CODTIENDA, $html);
        $html = ereg_replace("#NUMORDEN#", $NUMORDEN, $html);
        $html = ereg_replace("#MOUNT#", $MOUNT, $html);

        return $html;
    }

    function htmlRedirecFormEticket_bak($ETICKET) {
        $html = '<Html>
	<head>
	<title>Pagina prueba Visa</title>
	</head>
	<Body >

	<form name="fm" method="post" action="' . $this->_formularioVisa . '">
	    <input type="hidden" name="ETICKET" value="#ETICKET#" /><BR>
	    <input type="submit" name="boton" value="Pagar" /><BR>
	</form>
	#ETICKET#
	</Body>
	</Html>';

        $html = str_replace("#ETICKET#", $ETICKET, $html);

        return $html;
    }

    function htmlRedirecFormEticket($ETICKET) {
        
        $html = '<Html>
	<head>
	<title>Pagina prueba Visa</title>
	</head>
	<Body onload="fm.submit();">
        <img style="position: absolute;top: 17.5%;left: 19%; z-index: 1003;background-color: #FFFFFF;" src="/static/img/loading.gif" >
	<form name="fm" method="post" action="' . $this->_formularioVisa . '">
	    <input type="hidden" name="ETICKET" value="#ETICKET#" /><BR>
	</form>
	</Body>
	</Html>';

        $html = str_replace("#ETICKET#", $ETICKET, $html);
        return $html;
    }

}
