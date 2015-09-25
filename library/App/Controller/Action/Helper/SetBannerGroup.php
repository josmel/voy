<?php

class App_Controller_Action_Helper_SetBannerGroup extends Zend_Controller_Action_Helper_Abstract
{
    public function setBanners($params, $bannerType, $idUser, $mBanner, $mImage)
    {  
        $links = isset($params['txtLink']) ? $params['txtLink'] : array();
        $titles = isset($params['txtTitulo']) ? $params['txtTitulo'] : array();
        $states = isset($params['chkEstado']) ? $params['chkEstado'] : array();
        $images = isset($params['txtImagen']) ? $params['txtImagen'] : array();
        
        $ids = "";
        $i = 1;
        foreach($titles as $key => $title) {
            $dataItem = array();
            $dataItem['codtbanner'] = $bannerType['codtbanner'];
            $dataItem['titulo'] = $titles[$key];
            $dataItem['url'] = $links[$key];
            $dataItem['image'] = $images[$key];
            $dataItem['vchestado'] = isset($states[$key]) ?  : '0';
            $dataItem['norder'] = $i;
            $dataItem['idbanner'] = is_numeric($key) ? $key : 0;
            
            
            if ($dataItem['idbanner'] > 0) {
                //Update
                $dataItem['tmsfecmodif'] = date('Y-m-d H:i:s');
                $dataItem['vchusumodif'] = $idUser;
                $mBanner->update($dataItem, $dataItem['idbanner']);
            } else {
                if(isset($dataItem['image'])) {
//                    $resize = new Core_Utils_ResizeImage(
//                            ROOT_IMG_DINAMIC.'/banner/origin/'.$dataItem['image']
//                        );
//
//                    $resize->resizeImage(
//                            $bannerType['anchoimg'], $bannerType['altoimg'], 
//                            'exact'
//                        );
//                    
//                    $destinyFolder = ROOT_IMG_DINAMIC.'/banner/'.$bannerType['codproy']
//                        .'/'.$bannerType['anchoimg'].'x'.$bannerType['altoimg'];
//                    if(!file_exists($destinyFolder))
//                        mkdir($destinyFolder, 0777, true);
//                        
//                    $resize->saveImage($destinyFolder.'/'.$dataItem['image']);

                    $image = array(
                        'nombre' => $dataItem['image'],
                        'vchestado' => 1,
                        'vchusucrea' => $idUser
                    );
                    
                    $dataItem['idimagen'] = $mImage->insert($image);
                }
                
                //Registrar
                $dataItem['tmsfeccrea'] = date('Y-m-d H:i:s');
                $dataItem['vchusucrea'] = $idUser;
                $dataItem['idbanner'] = $mBanner->insert($dataItem);
            }
            
            $i++;
            $ids = $ids.($ids != '' ? ',' : '').$dataItem['idbanner'];
        }
        
        $mBanner->deleteAll($ids, $bannerType['codtbanner']);
    }
}