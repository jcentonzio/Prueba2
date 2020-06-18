<?php


namespace Movil\SolicitudGenerica\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Zend_Validate;

class FormGeneric extends AbstractHelper
{
    protected $_solicitudGenericaFactory;
    protected $_solicitudGenericaRepository;
    protected $_matriz;
    protected $_generic;
    protected $_integracionHelper;
    protected $_holiday;
    /**
       * Factory de Json
       *
       * @var \Magento\Framework\Controller\Result\JsonFactory
       */
    protected $_resultJsonFactory;
    protected $_horarioHelper;

    /**
     * Parametros de campañas SEM
     *
     * @var \General\CampaignParams\Helper\Data
     */

    protected $paramsCampaign;


    /*
  	 * ID de Tienda
  	 */
  	CONST STORE_ID = 1;

    public function __construct(
        \Movistar\Integracion\Helper\Integracion $integracionHelper,
        \Movil\SolicitudGenerica\Api\SolicitudGenericaRepositoryInterface $solicitudGenericaRepository,
        \Movil\SolicitudGenerica\Model\SolicitudGenericaFactory $solicitudGenericaFactory,
        \Movil\MatrizGeneric\Helper\FormGeneric $matriz,
        \MovistarEmp\Integracion\Helper\General $general,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \MovistarEmp\Integracion\Helper\Horario $horarioHelper,
        \General\Servicios\Helper\Holiday $holiday,
        \General\CampaignParams\Helper\Data $paramsCampaign
         ) {
        $this->_integracionHelper = $integracionHelper;
        $this->_solicitudGenericaRepository = $solicitudGenericaRepository;
        $this->_solicitudGenericaFactory = $solicitudGenericaFactory;
        $this->_matriz = $matriz;
        $this->_generic = $general;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_horarioHelper = $horarioHelper;
        $this->_holiday = $holiday;
        $this->paramsCampaign = $paramsCampaign;
    }

    public function getTokenEmp()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $variables = $objectManager->create('Magento\Variable\Model\Variable');

            $variables->setStoreId('planesmoviles');

            return $variables->loadByCode('token_planes_movistar')->getPlainValue();
        } catch (Exception $e) {
            echo "chp" . $e->getMessage();
        }
    }

    public function validacionIdLead($respuesta, $idSolicitud)
    {
        try {
            $horario = null;
            //Objeto JSON
            $resultJson = $this->_resultJsonFactory->create();

            $respuesta = preg_split('/(?<=\D)(?=\d)|\d+\K/', $respuesta);

            if (isset($respuesta[11]) && trim($respuesta[11]) != "0") {

                  /*guardamos el id lead en la ultima solicitud guardada*/
                $solicitudIngresada = $this->_solicitudGenericaRepository->getById($idSolicitud);
                //$solicitudIngresada = $solicitud->getById($idSolicitud);

                $solicitudIngresada->setData('id_lead', trim($respuesta[11]));
                $solicitudIngresada->save();

                $horario = $this->_horarioHelper->verifyTime();

                return $resultJson->setData(["status"    => true,"idlead"    => trim($respuesta[11]), 'horario'=> $horario]);
            } else {
                return $resultJson->setData(["status"    => false,"idlead"    => null, 'horario' => $horario]);
            }
        } catch (Exception $e) {
            return $resultJson->setData(["status"    => false,"idlead"    => null, 'horario' => null]);
        }
    }

    public function validStandarForm($post)
    {
        $post->rut = str_replace('.', '', $post->rut);

        try {
            if ($this->_integracionHelper->tokenPostVenta()) {
                $googleReCaptcha = $this->_integracionHelper->googleReCaptcha($post->captcha);

                if ($googleReCaptcha->success) {
                    if (isset($post->telefoContacto) || isset($post->mail) || isset($post->rut)) {
                        //validacion del email, rut, telefono

                        if (
                            Zend_Validate::is($post->mail, 'EmailAddress') &&
                            preg_match('/^([0-9]{7,8}-[0-9kK])$/', $post->rut) &&
                            preg_match('/^([0-9]{9})$/', $post->telefoContacto)) {
                            return true;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function setCampaign($campaign, $numberId)
    {
        if (preg_match('/\bAMPLIFFICA\b/', $campaign)) {
            $pixelStatus = $this->_integracionHelper->sendPixel($campaign, $numberId, 'PERSONALIZADO3');
            $this->_integracionHelper->updatePixelStatus($numberId, $pixelStatus);
        }
    }

    public function getHorario($horarioAgendamiento)
    {
        try {

            $store_id = self::STORE_ID;

  					$tomorrow = $this->_holiday->getDiaHabilSiguiente($store_id);

            $horarioAgen = preg_split("/[\s,+]+/", $horarioAgendamiento);

            $hora =  rand(intval($horarioAgen[0]), intval($horarioAgen[1]));

            $minuto = rand(0, 60);

            return [
                "date"    => $tomorrow,
                "time"    => $hora . ":" . $minuto . ":00"
                ];
        } catch (Exception $e) {
            echo "chp" . $e->getMessage();
        }
    }

    public function getData($post, $solicitud, $horario = ["date" => '', "time" => ''])
    {
        try {
            return $dataTest =
            [
                "Dataclient" => [
                    "rut"=> $post->rut,
                    "name"=> "",
                    "familyName"=>"",
                    "email"=> $post->mail,
                    "number" => $post->telefoContacto

                    ],

               "campana" => null,//$campaign,
               "idcamaleon" => 22013,
               "offer" => [
                      "name" => $solicitud[0]['nombre_producto'],
                      "description" => $solicitud[0]['nombre_producto'],
                      "price"=> $solicitud[0]['precio_total'],
                      "typeInstance" => "M",
                      "urlOffer" => $solicitud[0]['url']
                      ],
                "date" => [
                      "date"=> $horario['date'],
                      "time"=> $horario['time']
                     ]
            ];
        } catch (Exception $e) {
            echo "chp" . $e->getMessage();
        }
    }

    public function guardarSolicitud($post, $campaign)
    {
        try {

            $utm = $this->paramsCampaign->getParamsForm();

            date_default_timezone_set('America/Santiago');
            //$start_date = new DateTime();
            $date = new \DateTime();
            $temproduct =  $this->validacionOferta($post);
            $solicitud = $this->_solicitudGenericaFactory->create();
            $solicitud->setRut($post->rut);
            $solicitud->setTelefono($post->telefoContacto);
            $solicitud->setEmail($post->mail);
            $solicitud->setSkuEquipo($temproduct['equipo_sku']);
            $solicitud->setNombreEquipo($temproduct["nameproduct"]);
            $solicitud->setMemoriaInternaEquipo($temproduct["memoria_interna"]);
            $solicitud->setIdBelieve($temproduct["id_believe"]);
            $solicitud->setIdScl($temproduct["codigo_scl_plan"]);
            $solicitud->setNombrePlan($temproduct["nombre_plan"]);
            $solicitud->setPrecioPlanNormal($temproduct["precio_plan_normal"]);
            $solicitud->setTipoPago($temproduct['oferta_metodo_pago']);
            $solicitud->setPrecioPlanOferta($temproduct["precio_plan_oferta"]);
            $solicitud->setModalidad($temproduct['oferta_tipo_solicitud']);
            $solicitud->setMovistarOne($temproduct['oferta_movistar_one']);
            $solicitud->setFechaSolicitud($date);
            $solicitud->setPieEquipo($temproduct['oferta_precio_pie']);
            $solicitud->setColorEquipo($temproduct["color_equipo"]);
            $solicitud->setNumeroCuota($temproduct['oferta_numero_cuotas']);
            $solicitud->setCampanaAvatar($campaign);
            $solicitud->setCodigo($post->codigo);
            $solicitud->setTipoOferta($post->tipoOferta);
            $solicitud->setPrecioCuota($temproduct['oferta_numero_cuotas']);
            $solicitud->setPorcentajeDescuento($temproduct['oferta_porcentaje_descuento']);
            $solicitud->setPrecioTotal($temproduct['oferta_precio_total']);
            $solicitud->setPrecioReferencia($temproduct['oferta_precio_referencia']);
            $solicitud->setPrecioPie($temproduct['oferta_precio_pie']);
            $solicitud->setUtm($post->utm);
            $solicitud->setCampaign($utm['utms']['campaign']);
            $solicitud->setUtmCampaign($utm['utms']['utm_campaign']);
            $solicitud->setUtmSource($utm['utms']['utm_source']);
            $solicitud->setUtmContent($utm['utms']['utm_content']);
            $solicitud->setAdgroupid($utm['utms']['adgroupid']);
            $solicitud->setKeyword($utm['utms']['keyword']);

            $solic = $this->_solicitudGenericaRepository->save($solicitud);
            if ($this->_solicitudGenericaRepository->save($solicitud)) {
                return [
                'status' => true,
                "id" => $solic->getData('id'),
                    [
                    "nombre_producto"    => $temproduct["nameproduct"] . ' + ' . $temproduct["nombre_plan"],
                    "precio_total"        => $temproduct['oferta_precio_total'],
                    "url"                => $_SERVER['SERVER_NAME'] . '/equipomasplan/html?codigo=' . $post->codigo
                    ]
                ];
            } else {
                return ['status' => false, "id" => null];
            }
        } catch (Exception $e) {
            echo "chp" . $e->getMessage();
        }
    }

    private function sanitize($param)
    {
        return $this->_escaper->escapeXssInUrl(
            $this->_escaper->escapeHtml(
                $param
            )
        );
    }

    public function validacionOferta($post)
    {
        $resultJson = $this->_resultJsonFactory->create();

        if ($post->tipoOferta == "1") {
            $offer = $this->_matriz->getByCode($post->codigo);
            $plan = $this->_generic->getPlanByCode($offer['plan_codigo']);

            $temproduct = [
                "nameproduct"=> null,
                "memoria_interna" => null,
                "color_equipo" => null,
                "id_believe" => $plan->getData('codigo_believe'),
                "nombre_plan"=> $plan->getData('name'),
                "precio_plan_normal" => $plan->getData('precio_plan'),
                "precio_plan_oferta" => $plan->getData('precio_plan_oferta'),
                "equipo_sku" => null,
                "codigo_scl_plan" => $plan->getData('codigo_scl_plan'),
                "oferta_metodo_pago" => null,
                "oferta_tipo_solicitud" => null,
                "oferta_movistar_one" => null,
                "oferta_precio_pie" => null,
                "oferta_numero_cuotas" => null,
                "oferta_precio_cuota" => null,
                "oferta_porcentaje_descuento" => null,
                "oferta_precio_total" => null,
                "oferta_precio_referencia" => null,
                "oferta_precio_pie" => null
                ];
        } elseif ($post->tipoOferta == "2") {
            $offer = $this->_matriz->getByCode($post->codigo);
            $product = $this->_generic->getBySkuPrima($offer['equipo_sku']);
            $plan = $this->_generic->getPlanByCode($offer['plan_codigo']);

            $temproduct = [
                "nameproduct"=> $product->getName(),
                "memoria_interna" => $product->getResource()->getAttribute('memoria_interna')->getFrontend()->getValue($product),
                "color_equipo" => $product->getResource()->getAttribute('color_equipo')->getFrontend()->getValue($product),
                "id_believe" => $plan->getData('codigo_believe'),
                "nombre_plan"=> $plan->getData('name'),
                "precio_plan_normal" => $plan->getData('precio_plan'),
                "precio_plan_oferta" => $plan->getData('precio_plan_oferta'),
                "equipo_sku" => $offer["equipo_sku"],
                "codigo_scl_plan" => $plan->getData('codigo_scl_plan'),
                "oferta_metodo_pago" => $offer["oferta_metodo_pago"],
                "oferta_tipo_solicitud" => $offer["oferta_tipo_solicitud"],
                "oferta_movistar_one" => $offer["oferta_movistar_one"],
                "oferta_precio_pie" => $offer["oferta_precio_pie"],
                "oferta_numero_cuotas" => $offer["oferta_numero_cuotas"],
                "oferta_precio_cuota" => $offer["oferta_precio_cuota"],
                "oferta_porcentaje_descuento" => $offer["oferta_porcentaje_descuento"],
                "oferta_precio_total" => $offer["oferta_precio_total"],
                "oferta_precio_referencia" => $offer["oferta_precio_referencia"],
                "oferta_precio_pie" => $offer["oferta_precio_pie"],
                "costo_envio" => $offer["costo_envio"],
                "entity_padre" => $offer["equipo_entity_padre"]
                ];
        } elseif ($post->tipoOferta == "3") {
            $preciototal = 0;

            $codigos = implode("- ", (array_column($post->planFamilia['list'], 'codigo')));
            $names = implode("- ", (array_column($post->planFamilia['list'], 'name')));

            foreach ($post->planFamilia['planes'] as $key) {
                $preciototal += (floatval($key['precio']) * intval($key['total']));
                 $plan = $this->_generic->getPlanByCode($key['codigo']);
                 $codigo_scl_plan  = $plan->getData('codigo_scl_plan')."-";
            }

            $temproduct = [
                "nameproduct"=> null,
                "memoria_interna" => null,
                "color_equipo" => null,
                "codigo_scl_plan" => $codigo_scl_plan,
                "id_believe" => $codigos,
                "nombre_plan"=> "Plan-Familia+".$names,
                "precio_plan_normal" => null,
                "precio_plan_oferta" => null,
                "equipo_sku" => null,
                "oferta_metodo_pago" => null,
                "oferta_tipo_solicitud" => null,
                "oferta_movistar_one" => null,
                "oferta_precio_pie" => null,
                "oferta_numero_cuotas" => 12,
                "oferta_precio_cuota" => $post->planFamilia["precioTotal"],
                "oferta_porcentaje_descuento" => null,
                "oferta_precio_total" => $post->planFamilia["precioTotal"],
                "oferta_precio_referencia" => str_replace(".", "", $preciototal),
                "oferta_precio_pie" => null
                ];

        }

        return $temproduct;
    }


    public function dataStatic ($codigo){

        $offer = $this->_matriz->getByCode($codigo);

        if($offer) {

            return ["dnis" => $offer["asb_dnis"], "token" => $offer["asb_token"]];
        }else{

            return ["dnis" => "OFERTASMOVISTARMOVIL", "token" => "g9ZB%2F%2F7i02lxGSoCTuTBeB2%2FC2WaiyioqGWSnzQHLhQ"];
        }
    }
}
