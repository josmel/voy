<?php

/**
 * Description of Pasarela
 *
 * @author Josmel
 */
class App_Controller_Action_Helper_PasarelaMasterCard extends Zend_Controller_Action_Helper_Abstract {

    //put your code here
    public function __construct() {
        $this->_config = Zend_Registry::get('config');
        $this->_KeyMerchant = $this->_config['curl']['pasarela']['KeyMerchant'];
        //$this->_Curl = new Zend_Http_Client_Adapter_Curl();
    }

    public function generarMac($arrDatosFinish) {
        $arrDatosFinish[] = $this->_KeyMerchant;
        $Cadenafinal = implode('', $arrDatosFinish);
        $strHash = urlencode(base64_encode($this->hmacsha1($this->_KeyMerchant, $Cadenafinal)));
        $arrDatosFinish[9] = $strHash;
        var_dump($arrDatosFinish);
        exit;
        $result = $this->htmlRedirecFormAnt($arrDatosFinish);
        echo $result;
    }

    function htmlRedirecFormAnt($arrDatosFinish) {
        $html = '<Html>
	<head>
	<title>Pagina prueba MasterCard</title>
	</head>
	<Body onload="fm.submit();">
        <img style="position: absolute;top: 17.5%;left: 19%; z-index: 1003;background-color: #FFFFFF;" src="/static/img/loading.gif" >
	<form name="fm" method="post" action="' . $this->_config['curl']['pasarela']['url'] . '">
	    <input type="hidden" name="I1" value="#I1#" /><BR>
	    <input type="hidden" name="I2" value="#I2#" /><BR>
	    <input type="hidden" name="I3" value="#I3#" /><BR>
            <input type="hidden" name="I4" value="#I4#" /><BR>
	    <input type="hidden" name="I5" value="#I5#" /><BR>
	    <input type="hidden" name="I6" value="#I6#" /><BR>
            <input type="hidden" name="I7" value="#I7#" /><BR>
	    <input type="hidden" name="I8" value="#I8#" /><BR>
	    <input type="hidden" name="I9" value="#I9#" /><BR>
            <input type="hidden" name="I10" value="#I10#" /><BR>
	</form>
	</Body>
	</Html>';

        $html = str_replace("#I1#", $arrDatosFinish[0], $html);
        $html = str_replace("#I2#", $arrDatosFinish[1], $html);
        $html = str_replace("#I3#", $arrDatosFinish[2], $html);
        $html = str_replace("#I4#", $arrDatosFinish[3], $html);
        $html = str_replace("#I5#", $arrDatosFinish[4], $html);
        $html = str_replace("#I6#", $arrDatosFinish[5], $html);
        $html = str_replace("#I7#", $arrDatosFinish[6], $html);
        $html = str_replace("#I8#", $arrDatosFinish[7], $html);
        $html = str_replace("#I9#", $arrDatosFinish[8], $html);
        $html = str_replace("#I10#", $arrDatosFinish[9], $html);
        return $html;
    }

    function hmacsha1($key, $data, $hex = false) {
        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen($key) > $blocksize)
            $key = pack('H*', $hashfunc($key));
        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
        if ($hex == false) {
            return $hmac;
        } else {
            return bin2hex($hmac);
        }
    }

//    function hmacsha1($key, $data, $hex = true) {
//        if (strlen($key) > 64)
//            $key = pack('H*', sha1($key));
//        $key = str_pad($key, 64, chr(0x00));
//        $ipad = str_repeat(chr(0x36), 64);
//        $opad = str_repeat(chr(0x5c), 64);
//        $hmac = pack('H*', sha1(($key ^ $opad) . pack('H*', sha1(($key ^ $ipad) . $data))));
//        if ($hex == false) {
//            return $hmac;
//        } else {
//            return bin2hex($hmac);
//        }
//    }

    function shipping($arrDatosFinish) {
        $arrDatos['I1'] = $arrDatosFinish[0];
        $arrDatos['I2'] = $arrDatosFinish[1];
        $arrDatos['I3'] = $arrDatosFinish[2];
        $arrDatos['I4'] = $arrDatosFinish[3];
        $arrDatos['I5'] = $arrDatosFinish[4];
        $arrDatos['I6'] = $arrDatosFinish[5];
        $arrDatos['I7'] = $arrDatosFinish[6];
        $arrDatos['I8'] = $arrDatosFinish[7];
        $arrDatos['I9'] = $arrDatosFinish[8];
        $arrDatos['I10'] = $arrDatosFinish[9];
        try {
            $adapter = new Zend_Http_Client_Adapter_Curl();
            $adapter->setConfig($this->_config['curl']['pasarela']['options']);
            $client = new Zend_Http_Client($this->_config['curl']['pasarela']['url']);
            $client->setAdapter($adapter);
            $client->setParameterPost($arrDatos);
            $response = $client->request(Zend_Http_Client::POST);
            if (strpos($response, "<h5>Successfully subscribed:</h5>") !== false) {
                echo "<p>Added  to trainee email list successfully.</p>";
                exit;
            } else if (strpos($response, "Already a member") !== false) {

                echo "<p>Already a member of the trainee email list";
            } else {
                var_dump($response);
                exit;
                echo 'enviado saltifactoriamente';
                exit;
            }
        } catch (Zend_Http_Client_Adapter_Curl_Exception $e) {
            echo $e;
            exit;
        } catch (Exception $ex) {
            echo $ex;
            exit;
        }
    }

    public function respuestaMaster($dataForm) {

        if ($dataForm['O1'] == 'A') {
            $HashPuntoWeb = urldecode($dataForm['O20']); //HashSha1 PuntoWeb
            $arrDatos[] = $dataForm['O1'];
            $arrDatos[] = $dataForm['O2'];
            $arrDatos[] = $dataForm['O3'];
            $arrDatos[] = $dataForm['O8'];
            $arrDatos[] = $dataForm['O9'];
            $arrDatos[] = $dataForm['O10'];
            $arrDatos[] = $dataForm['O13'];
            $arrDatos[] = $dataForm['O15'];
            $arrDatos[] = $dataForm['O18'];
            $arrDatos[] = $dataForm['O19'];
            $arrDatos[] = $this->_KeyMerchant;
            //Concatenando Outputs
            $Cadenafinal = implode('', $arrDatos);
            //Generar Mac;
            $strHash = base64_encode($this->hmacsha1($this->_KeyMerchant, $Cadenafinal));
            //Validacion de hash;
            if ($HashPuntoWeb == $strHash) {
                return 1;
                //Hash valido
            } else {
                return 2;
                //hash invalido
            }
        } else {
            return 3;
            //TRANSACCION DENEGADA
        }
    }

}
