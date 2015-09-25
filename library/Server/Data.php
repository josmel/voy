<?php

ini_set("soap.wsdl_cache_enabled", 0);
/**
 * Esta clase contiene la función que será utilizado por el servicio de llamadas Web.
 * Todas las lógicas empresariales serán implented o llama en estas funciones. 
 * 
 * @author Josmel Yupanqui
 *
 */

/**
 * Movistar Admin Web service

 */
class Server_Data {

    public function __construct() {
        $this->_config = Zend_Registry::get('config');
    }

    /**
     * service
     * @param string $Servicio
     * @return array
     */
    public function receiveMessage($Servicio) {
        switch ($Servicio) {
            case 'BANNER':
                $util = new Server_Util();
                $ModelBanner = new Admin_Model_Banner();
                $totalBanners = $ModelBanner->BannersAll();
                $response = $util->groupArray($totalBanners, 'posicion');
                break;
            case 'MUSICA':
                $ModelMusica = new Admin_Model_Musica();
                $response = $ModelMusica->MusicaAll();
                break;
            case 'JUEGO':
                $ModelJuego = new Admin_Model_Juego();
                $response = $ModelJuego->JuegoAll();
                break;
            case 'TEXT':
                $ModelText = new Admin_Model_Text();
                $response = $ModelText->textAll();
                break;
            case 'SERVICIO':
                $ModelServicio = new Admin_Model_Servicio();
                $response = $ModelServicio->ServicioAll();
                break;
            default:
                break;
        }
        return $response;
    }

}
