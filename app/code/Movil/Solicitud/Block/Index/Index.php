<?php

namespace Movil\Solicitud\Block\Index;

/**
 * Bloque de Formulario Catalogo
 *
 * @author     VASS Magento
 */
class Index extends \Magento\Framework\View\Element\Template
{

    /**
     * Request Interface
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * Product Model
     *
     * @var \Magento\Catalog\Model\Product $productModel
     */
    protected $_productModel;

    /**
     * Store Manager Interface
     *
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * Store Manager Interface
     *
     * @var \Magento\Variable\Model\Variable
     */
    protected $_storeVariable;

    /**
     * Helper de Horario
     *
     * @var \MovistarEmp\Integracion\Helper\Horario
     */
    protected $horarioHelper;

    /**
     * Helper General Movil
     *
     * @var \Movil\General\Helper\Index
     */
    protected $movilGeneral;

    /**
     * Mantenedor de Bloques
     *
     * @var \General\MantenedorBloques\Helper\Data
     */
    protected $mantenedorBloques;

    /**
     * Matriz Generica Interfaz
     *
     * @var \Movil\MatrizGeneric\Api\MatrizGenericRepositoryInterface
     */
    protected $matrizGenericInterface;

    /**
     * Información de la oferta
     *
     * @var array
     */
    protected $_offerInfo = [];

    protected $_helperFormGeneric;

    /**
     * [protected FUncion que me devuelve el horario disponible]
     * @var [type]
     */
    protected $_helperHoliday;

    /**
     * [protected variables para las funciones disponibles.]
     * @var [type]
     */
    protected $_horario_disponible;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Parametros de campanas SEM
     *
     * @var \General\CampaignParams\Helper\Data
     */

    protected $paramsCampaign;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param \Magento\Variable\Model\Variable $storeVariable
     * @param \MovistarEmp\Integracion\Helper\Horario $horarioHelper
     * @param \Movil\General\Helper\Index $movilGeneral
     * @param \General\MantenedorBloques\Helper\Data $mantenedorBloques
     * @param \Movil\MatrizGeneric\Api\MatrizGenericRepositoryInterface $matrizGenericInterface
     */
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\App\Request\Http $request,
            \Magento\Catalog\Model\Product $productModel,
            \Magento\CatalogInventory\Api\StockStateInterface $stockState,
            \Magento\Variable\Model\Variable $storeVariable,
            \MovistarEmp\Integracion\Helper\Horario $horarioHelper,
            \Movil\General\Helper\Index $movilGeneral,
            \General\MantenedorBloques\Helper\Data $mantenedorBloques,
            \Movil\MatrizGeneric\Api\MatrizGenericRepositoryInterface $matrizGenericInterface,
            \Movil\MatrizGeneric\Helper\FormGeneric $helperFormGeneric,
            \General\Servicios\Helper\Holiday $helperHoliday,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\Registry $registry,
            \General\CampaignParams\Helper\Data $paramsCampaign

        ) {
        $this->_request = $request;
        $this->_productModel = $productModel;
        $this->_stockState = $stockState;
        $this->_storeVariable = $storeVariable;
        $this->_movilGeneral = $movilGeneral;
        $this->_horarioHelper = $horarioHelper;
        $this->_mantenedorBloques = $mantenedorBloques;
        $this->_matrizGenericInterface = $matrizGenericInterface;
        $this->_helperFormGeneric = $helperFormGeneric;
        $this->_helperHoliday = $helperHoliday;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->paramsCampaign = $paramsCampaign;
        parent::__construct($context);
    }

    /**
    * Valida la oferta, prepara la data y setea el SEO
    *
    * @return object
    */
    protected function _prepareLayout()
    {
        //Se obtiene el codigo de la URL

        $tipo = $this->_request->getParam('tipo');

        // echo 'Tipo: '.$tipo.'<br>';
        $this->_offerInfo['planesFamilia'] =
            [
                'planes' => [],
                'list' => [],
                'planTotal' => [],
                'total' => 0
             ];

        $utm=$this->_registry->registry("pm_origin_utm");

        if ($tipo == 'f') {
            $planCodigos = preg_replace('/[^a-zA-ZñÑ\d_ ]/', '', $this->_request->getParam('plan'));

            //Si es array
            if (!is_array($planCodigos)) {
                $this->invalidUrl();
            }

            //1 XL
            //?tipo=f&plan%5B0%5D=JSE_Multimedia+XL+60GB

            //1 HD y 1 XL
            //?tipo=f&plan%5B0%5D=JN9_Multimedia+ilimitado+HD&plan%5B1%5D=JSE_Multimedia+XL+60GB

            //2 XL
            //?tipo=f&plan%5B0%5D=JSE_Multimedia+XL+60GB&plan%5B1%5D=JSE_Multimedia+XL+60GB

            //2 XL y 1 HD
            //?tipo=f&plan%5B0%5D=JSE_Multimedia+XL+60GB&plan%5B1%5D=JSE_Multimedia+XL+60GB&plan%5B2%5D=JN9_Multimedia+ilimitado+HD

            //2HD y 2XL
            //?tipo=f&plan%5B0%5D=JN9_Multimedia+ilimitado+HD&plan%5B1%5D=JN9_Multimedia+ilimitado+HD&plan%5B2%5D=JSE_Multimedia+XL+60GB&plan%5B3%5D=JSE_Multimedia+XL+60GB

            //5 Planes
            //?tipo=f&plan%5B0%5D=JN9_Multimedia+ilimitado+HD&plan%5B1%5D=JN9_Multimedia+ilimitado+HD&plan%5B2%5D=JSE_Multimedia+XL+60GB&plan%5B3%5D=JSE_Multimedia+XL+60GB&plan%5B4%5D=JSE_Multimedia+XL+60GB

            //1XL, 1L, 1M, 1S
            //?tipo=f&plan%5B0%5D=JSE_Multimedia+XL+60GB&plan%5B1%5D=JSD_Plan+Multimedia+L+35GB&plan%5B2%5D=JSC_Plan+Multimedia+M+25GB&plan%5B3%5D=JSB_Plan+Control+S+18GB

            $planMaxKey = [
                                                    'value' => '',
                                                    'price' => 0
                                                ];

            if (!empty($planCodigos)) {
                foreach ($planCodigos as $planCodigo) {
                    $plan = $this->_movilGeneral->getPlanByCode($planCodigo);

                    if ($plan) {
                        if (array_key_exists($planCodigo, $this->_offerInfo['planesFamilia']['planes'])) {
                            $this->_offerInfo['planesFamilia']['planes'][$planCodigo]['total']++;
                        } else {
                                    //Información del plan
                            $planData =
                                    [
                                        'name' => $plan->getName(),
                                        'precio' => $this->_movilGeneral->formatPrice($plan->getPrecioPlan()),
                                       'precioOferta' => $this->_movilGeneral->formatPrice(round($plan->getPrecioPlan()/2, 0, PHP_ROUND_HALF_UP)),
                                        'modal' => $plan->getEmpPlanFullDescription(),
                                        'total' => 1,
                                        'codigo'=> $planCodigo
                                    ];

                            if ($plan->getPrecioPlan() > $planMaxKey['price']) {
                                $planMaxKey =
                                    [
                                        'key' => $planCodigo,
                                        'price' => $plan->getPrecioPlan()
                                    ];
                            }

                            $this->_offerInfo['planesFamilia']['planes'][$planCodigo] = $planData;
                        }

                        $this->_offerInfo['planesFamilia']['total']++;
                    }
                }
            }

            if ($this->_offerInfo['planesFamilia']['total'] < 2 || $this->_offerInfo['planesFamilia']['total'] > 4) {
                $this->invalidUrl();
            }

            uasort($this->_offerInfo['planesFamilia']['planes'], function ($a, $b) {
                return $a['precio'] <= $b['precio'];
            });

            $this->_offerInfo['planesFamilia']['list'] = [];
            foreach ($this->_offerInfo['planesFamilia']['planes'] as $key => $list) {
                $this->_offerInfo['planesFamilia']['list'][$key] =
                    [
                        'name' => $list['name'],
                        'total' => $list['total'],
                        'codigo' => $list['codigo']
                    ];
            }

            $this->_offerInfo['planesFamilia']['planTotal'] =
                [
                    'name' => $this->_offerInfo['planesFamilia']['planes'][$planMaxKey['key']]['name'],
                    'precio' => $this->_movilGeneral->formatPrice($planMaxKey['price']),
                    'modal' => $this->_offerInfo['planesFamilia']['planes'][$planMaxKey['key']]['modal'],
                    'codigo' => $this->_offerInfo['planesFamilia']['planes'][$planMaxKey['key']]['codigo']
                ];

            if ($this->_offerInfo['planesFamilia']['planes'][$planMaxKey['key']]['total'] > 1) {
                $this->_offerInfo['planesFamilia']['planes'][$planMaxKey['key']]['total']--;
            } else {
                unset($this->_offerInfo['planesFamilia']['planes'][$planMaxKey['key']]);
            }
            //Precio Total de la Oferta
            $this->_offerInfo['planesFamilia']['precioTotal'] = 0;
            foreach ($this->_offerInfo['planesFamilia']['planes'] as $list) {
                $this->_offerInfo['planesFamilia']['precioTotal'] += preg_replace('/[^0-9]/', '', $list['precioOferta'])*$list['total'];
            }

            $this->_offerInfo['planesFamilia']['precioTotal'] += preg_replace('/[^0-9]/', '', $this->_offerInfo['planesFamilia']['planTotal']['precio']);

            $this->_offerInfo['planesFamilia']['precioTotal'] = $this->_movilGeneral->formatPrice($this->_offerInfo['planesFamilia']['precioTotal']);

            $this->_offerInfo['general'] =
                        [
                            'costoEnvio' => '0',
                            'tipoOferta' => '3'
                        ];
        } else {
            $codigo = $this->_request->getParam('codigo');

            //Si se tiene el codigo

            if ($codigo) {
                try {

                                    /**** ACA SE CARGA LA OFERTA*****/

                    $offer = $this->_helperFormGeneric->getByCode($codigo);

                    //$offer = $this->_matrizGenericInterface->getById(2);//Obtención de oferta
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->invalidUrl();
                }

                //Si existe la oferta
                if ($offer) {

                    //Plan
                    if ($offer['tipo_oferta'] == 1) {
                        $this->setPlanInfo($offer);

                        if (!array_key_exists('plan', $this->_offerInfo)) {
                            $this->invalidUrl();
                        }
                        $this->_offerInfo['plan']['codigo'] = $codigo;
                        $this->_offerInfo['offer']['tipoSolicitud'] = $offer['oferta_tipo_solicitud'];
                        $this->_offerInfo['general']['tipoOferta'] = 1;
                        $this->setGeneralInfo($offer);

                        //Equipo
                    } else {
                        $this->_offerInfo['general']['tipoOferta'] = 2;
                        // echo 'tipo oferta 2: ';

                        $this->setPlanInfo($offer);
                        $this->setEquipoInfo($offer);
                        $this->setOfferInfo($offer);

                        if (!(
                                array_key_exists('plan', $this->_offerInfo) &&
                                array_key_exists('equipo', $this->_offerInfo) &&
                                array_key_exists('equipoPadre', $this->_offerInfo)
                                )
                            ) {
                            $this->invalidUrl();
                        }

                        $this->setGeneralInfo($offer);
                    }
                } else {
                    $this->invalidUrl();
                }
            } else {
                $this->invalidUrl();
            }
        }

        //Horarios de Agendamiento
        $this->_offerInfo['horariosAgen'] = json_decode($this->_storeVariable->loadByCode('emp_rango_horario')->getPlainValue());

        //Horario disponible si es o no feriado
        $this->_offerInfo['horario_disponible'] = $this->_helperHoliday->getScheduleAvaliable($this->_storeManager->getStore()->getId());

        //Horario actual
        $this->_offerInfo['horario'] = $this->_horarioHelper->getStatus($this->_offerInfo['horario_disponible']);
        $this->_offerInfo['utm'] = $utm;

        //Parametros de campañas SEM
        $this->_offerInfo['utms'] = $this->setUtmParams();

        return parent::_prepareLayout();
    }

    /**
     * Redirect por URL invalidad
     *
     * @return array
     */
    private function invalidUrl()
    {

        //Se busca la URL de equipo no encontrado
        $urlEquipoNoEncontrado  = $this->_storeVariable->loadByCode('emp_url_equipo_no_encontrado')->getPlainValue();

        header('Location: '.$urlEquipoNoEncontrado);
        die();
    }

    /**
     * Información de la oferta
     *
     * @return array
     */
    public function getInfo()
    {
        return $this->_offerInfo;
    }

    /**
     * Bloque de Equipo mas Plan
     *
     * @param string $identificador
     * @return string
     */
    public function getMantenedorBlock($identificador)
    {
        $text = $this->_mantenedorBloques->getContenidoEstatico($identificador, 7);
        $text = preg_replace('/\[\[/', '{{', $text);
        $text = preg_replace('/\]\]/', '}}', $text);

        return $text;
    }

    private function setUtmParams()
    {
        return $this->paramsCampaign->getParamsCampaign();
    }

    private function setPlanInfo($offer)
    {
        $plan = $this->_movilGeneral->getPlanByCode($offer['plan_codigo']);

        if ($plan) {
            //Información del plan
            $this->_offerInfo['plan'] = [
                                        'name' => $plan->getName(),
                                        'precio' => $this->_movilGeneral->formatPrice($plan->getPrecioPlan()),
                                        'precioOferta' => $this->_movilGeneral->formatPrice($offer['plan_precio_oferta']),
                                        'duracion' => $offer['plan_duracion_oferta'],
                                        'modal' => $plan->getEmpPlanFullDescription()
                                        ];
        }
    }

    private function setEquipoInfo($offer)
    {
        $activeProduct = true;

        try {

                        //Se obtiene el producto padre
            $parentProduct = $this->_productModel->load($offer['equipo_entity_padre']);

            //Se obtiene el equipo hijo
            $product = $this->_movilGeneral->getBySkuPrima($offer['equipo_sku']);

            if ($product) {
                //Se verifica el stock y si esta habilitado
                if (!($product->isSaleable() && $this->_stockState->verifyStock($product->getId()))) {
                    $activeProduct = false;
                }
            } else {
                $activeProduct = false;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $activeProduct = false;
        }

        if ($activeProduct) {
            //Información del equipo
            $this->_offerInfo['equipo'] = [
                                            'id' => $product->getId(),
                                            'name' => $product->getFullname(),
                                            'codigoColor' => $product->getData('codigo_color'),
                                            'nombreColor' => ucwords( $product->getResource()->getAttribute('color_equipo')->getFrontend()->getValue($product)),
                                            'tipoProducto' => $product->getResource()->getAttribute('emp_tipo_producto')->getFrontend()->getValue($product),
                                            'capacidad' => $product->getResource()->getAttribute('memoria_interna')->getFrontend()->getValue($product),
                                            'seguroMovistarOne' => $product->getSeguroMovistarOne()
                                          ];

            //Información del equipo padre
            $this->_offerInfo['equipoPadre'] = [
                                                    'id' => $parentProduct->getId(),
                                                    'name' => $parentProduct->getFullname(),
                                                    'marca' => $parentProduct->getResource()->getAttribute('marcas')->getFrontend()->getValue($parentProduct),
                                                ];
}
    }

    private function setOfferInfo($offer)
    {

            //Información de la oferta
        $this->_offerInfo['offer'] = [
                                        'codigo' => $offer['codigo'],
                                        'tipoSolicitud' => $offer['oferta_tipo_solicitud'],
                                        'metodoPago' => $offer['oferta_metodo_pago'],
                                        'numeroCuotas' => $offer['oferta_numero_cuotas'],
                                        'precioCuota' => $this->_movilGeneral->formatPrice($offer['oferta_precio_cuota']),
                                        'precioTotal' => $this->_movilGeneral->formatPrice($offer['oferta_precio_total']),
                                        'precioPie' => $this->_movilGeneral->formatPrice($offer['oferta_precio_pie']),
                                        'precioReferencia' => $this->_movilGeneral->formatPrice($offer['oferta_precio_referencia']),
                                        'movistarOne' => $offer['oferta_movistar_one'],
                                    ];
    }

    public function setGeneralInfo($offer)
    {
        $this->_offerInfo['general'] = [
                                            'costoEnvio' => ($offer['costo_envio'] != null) ? $this->_movilGeneral->formatPrice($offer['costo_envio']) : '',
                                            'tipoOferta' => $offer['tipo_oferta']
                                        ];
    }
}
