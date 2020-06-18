<?php

/**
 * Helper para Adminhtml - Movistar - Helper
 */

namespace Movil\SolicitudGenerica\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * SampleModule base helper
 */
class Solicitudes extends AbstractHelper
{
    protected $_fileFactory;

    public function __construct(
    \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
    }

    /**
     * Exportar Pedidos
     *
     */
    public function export($fechaInicio, $fechaFin)
    {
        $fechaInicio = date("Y-m-d H:i:s", strtotime($fechaInicio));
        $fechaFin = date("Y-m-d H:i:s", strtotime($fechaFin . ' + 1 day'));

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = 'SELECT *
    FROM movistar_solicitud_generic
    WHERE fecha_solicitud >= "' . $fechaInicio . '"
    AND fecha_solicitud <= "' . $fechaFin . '"';
        $result = $connection->fetchAll($sql);

        $values = [];

        foreach ($result as $key) {
            if ($key['modalidad'] === '1') {
                $tipoSolicitud = 'Portabilidad';
            } elseif ($key['modalidad'] === '3') {
                $tipoSolicitud = 'Alta';
            } else {
                $tipoSolicitud = '-';
            }

            if ($key['movistar_one'] == 1) {
                $movistarOne = 'Si';
            } elseif ($key['movistar_one'] == 0) {
                $movistarOne = 'No';
            } else {
                $movistarOne = '-';
            }

            $tipoPago = null;

            if ($key['tipo_pago'] == 1) {
                $tipoPago = 'Pago Externo';
            } elseif ($key['tipo_pago'] == 2) {
                $tipoPago= 'Contra boleta';
            } elseif ($key['tipo_pago'] == 3) {
                $tipoPago = 'Movistar One';
            } elseif ($key['tipo_pago'] == 4) {
                $tipoPago = 'WiFi';
            }
            $utms=$this->getMergeUtm($key['utm']);

            $info =
        $key['id'] . ',' .
        $key['rut'] . ',' .
        $key['telefono'] . ',' .
        $key['email'] . ',' .
        $key['sku_equipo'] . ',' .
        $key['nombre_equipo'] . ',' .
        $key['color_equipo'] . ',' .
        $key['memoria_interna_equipo'] . ',' .
        //Información Plan
        $key['nombre_plan'] . ',' .
        $key['precio_plan_normal'] . ',' .
        $key['precio_plan_oferta'] . ',' .
        //Información oferta
        $key['pie_equipo'] . ',' .
        $key['precio_total'] . ',' .
        $key['precio_referencia'] . ',' .
        $key['precio_cuota'] . ',' .
        $key['numero_cuota'] . ',' .
        $key['porcentaje_descuento'] . ',' .
        $tipoSolicitud . ',' .
        $movistarOne . ',' .
        $tipoPago . ',' .
        $key['codigo'] . ',' .
        $key['id_lead'] . ',' .
        $key['campana_avatar'] . ',' .
        $key['id_believe'] . ',' .
        $key['id_scl'] . ',' .
        $key['fecha_solicitud'] . ',' .
        $utms . ',' .
        $key['campaign'] . ',' .
        $key['utm_campaign'] . ',' .
        $key['utm_source'] . ',' .
        $key['utm_content'] . ',' .
        $key['adgroupid'] . ',' .
        $key['keyword'] . ',' ;

            if ($key['rut'] && $key['telefono']) {
                array_push($values, $info);
            }
        }
        
        $heads = 'ID,RUT,TELEFONO,EMAIL,SKU EQUIPO,NOMBRE EQUIPO,COLOR EQUIPO,MEMORIA INTERNA,NOMBRE PLAN,PRECIO PLAN NORMAL,PRECIO PLAN OFERTA,PRECIO PIE,PRECIO TOTAL,PRECIO REFERENCIA,PRECIO CUOTA,NUMERO DE CUOTAS,PORCENTAJE DESCUENTO,MODALIDAD,MOVISTAR ONE,METODO DE PAGO,CODIGO,ID LEAD,CAMPANA,ID BELIEVE,ID SCL,FECHA SOLICITUD,UTM,CAMPAIGN,UTM_CAMPAIGN,UTM_SOURCE,UTM_CONTENT,ADGROUPID,KEYWORD';

        $colValues = implode(PHP_EOL, $values);

        $data =  $heads . PHP_EOL . $colValues;

        //Se crea el archivo
        $this->_fileFactory->create(
      'SolicitudesGenericas_entre_' . date("d_m_Y", strtotime($fechaInicio)) . '_y_' . date("d_m_Y", strtotime($fechaFin)) . '.csv',
      $data,
      DirectoryList::VAR_DIR,
      'application/octet-stream'
      );
    }

    public function getMergeUtm($utms)
    {
        $data = false;
        $utms=json_decode($utms, true);
        if (is_array($utms) && count($utms) > 0) {
            foreach ($utms as $key => $value) {
                if (is_array($value) && count($value) > 0) {
                    $text = array_values($value)[0];
                    $variable = array_keys($value, $text)[0];
                    $data .= ucwords($variable) . " = " . ucwords($text) . " ";
                }
            }
        }
        return $data ? $data : "";
    }

    public function clean($string)
    {
        return preg_replace('/[^A-Za-z0-9\- ]/', '', $string); // Removes special chars.
    }

    public function cleanEmail($string)
    {
        return preg_replace('/[^A-Za-z0-9\-@.]/', '', $string); // Removes special chars.
    }

    public function cleanIP($string)
    {
        return preg_replace('/[^0-9,.]/', '/', $string); // Removes special chars.
    }
}
