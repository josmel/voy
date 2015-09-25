<?php


class Voy_PasarelaController extends App_Controller_ActionVoy {


    public function init() {
        parent::init();
        $this->_pasarelaVisa = $this->_helper->getHelper('PasarelaVisa');
        $this->_pasarelaMasterCard = $this->_helper->getHelper('PasarelaMasterCard');
        $this->_mastercard = $this->_config['app']['mastercard'];
        $this->_visa = $this->_config['app']['visa'];
    }

    public function pagarPasarelaAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                switch ($dataForm["medio"]) {
                    case 'visa':
                        $arrDatos[] = rand(111111, 999999); //$_POST["numPedido"];
                        $arrDatos[] = '45.30';
                        $arrDatos[] = 'josmel noel';
                        $arrDatos[] = 'yupanqui huaman';
                        $arrDatos[] = 'Lima';
                        $arrDatos[] = 'Av los Algarrobos Condominio Parques De Villa';
                        $arrDatos[] = 'josmelnyh89@gmail.com';
                        $arrDatos[] = "PRUEBA VISANET";
                        $this->_pasarelaVisa->GenerarTiket($arrDatos);
                        exit;
                        break;
                    case 'mastercard':
                        $arrDatos[] = 2160935;
                        $arrDatos[] = 'ORD0001';
                        $arrDatos[] = '22.50';
                        $arrDatos[] = 'USD';
                        $arrDatos[] = '20151027';
                        $arrDatos[] = '104512';
                        $arrDatos[] = 'A1234565432';
                        $arrDatos[] = 'REG0001';
                        $arrDatos[] = 'PER';
                        $this->_pasarelaMasterCard->generarMac($arrDatos);
                        break;
                    default:
                        break;
                }
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        }
    }

    public function respuestaVisaAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                if (isset($dataForm["eticket"])) {
                    $result = $this->_pasarelaVisa->ConsultaEticket($dataForm["eticket"]);
                    $file = fopen($this->_visa, "a") or die("Problemas");
                    fwrite($file, json_encode($result, 128) . PHP_EOL);
                    fclose($file);
                    $this->view->respuestaVisa = $result;
                }
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            echo 'PAGINA NO PERMITIDA';
            exit;
        }
    }

    public function respuestaMasterCardAction() {
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            $file = fopen($this->_mastercard, "a") or die("Problemas");
            fwrite($file, json_encode($dataForm) . PHP_EOL);
            fclose($file);
            try {
                if (isset($dataForm)) {
                    $result = $this->_pasarelaMasterCard->respuestaMaster($dataForm);
                    $file = fopen($this->_mastercard, "a") or die("Problemas");
                    fwrite($file, $result . PHP_EOL);
                    fclose($file);
                    $this->view->respuesta = $dataForm;
                }
            } catch (Exception $e) {
                $this->_flashMessage->error($e->getMessage());
            }
        } else {
            echo 'ACCESO DENEGADO GET';
            exit;
        }
    }


}