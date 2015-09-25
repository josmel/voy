<?php

class Voy_SelectController extends App_Controller_ActionVoy {

    public function init() {
        parent::init();
        $this->_Services = new App_Controller_Services();
    }

    public function selectDependienteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = $this->_getAllParams();
        $departamento = isset($params['departamento']) ? $params['departamento'] : null;
        $selectDestino = isset($params['select']) ? $params['select'] : null;
        $opcionSeleccionada = isset($params['opcion']) ? $params['opcion'] : null;
        $listadoSelects = array(
            "departamento" => "ListarDepartamentos",
            "provincia" => "ListarProvincias",
            "distrito" => "ListarDistritos"
        );
        if ($this->_Services->validaSelect($selectDestino) && $this->_Services->validaOpcion($opcionSeleccionada)) {
            $tabla = $listadoSelects[$selectDestino];
            if (isset($departamento) and ! empty($departamento)) {
                $data = $this->_Services->$tabla($departamento, $opcionSeleccionada);
            } else {
                $data = $this->_Services->$tabla($opcionSeleccionada);
            }
            echo "<label> " . ucwords($selectDestino) . "</label>";
            echo "<select name='" . $selectDestino . "' id='" . $selectDestino . "' onChange='cargaContenido2(this.id,$opcionSeleccionada)' class='form-control' required>";
            echo "<option value=''>Selecciona opci&oacute;n...</option>";
            foreach ($data as $indice => $registro) {
                echo "<option value='" . $registro[$selectDestino] . "'>" . $registro['descripcion'] . "</option>";
            }
            echo "</select>";
        };
        exit;
    }

}
