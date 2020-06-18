define(
    [
        'jquery',
        'validateMovil',
        'vueMovil',
        'VueRecaptchaMovil',
        'tagManagerMovil',
        'mage/translate'
    ],
    function(
        $,
        validate,
        Vue,
        VueRecaptcha,
        tagManagerMovil
    ) {

        var app = new Vue({
            el: '#v1Form',
            data: {
                general: solicitudInfo.general,
                horario: solicitudInfo.horario,
                horariosAgen: solicitudInfo.horariosAgen,
                planesFamilia: solicitudInfo.planesFamilia,
                planTotal: solicitudInfo.planesFamilia.planTotal,
                sitekey: '6Le19kEUAAAAAGkFF4p2lZXsqfU_fP5c6cdQrNWq',
                typeScreen: 'FORMULARIO',
                equipo: solicitudInfo.equipo,
                offer: solicitudInfo.offer,
                plan: solicitudInfo.plan,
                equipoPadre: solicitudInfo.equipoPadre,
                reintentar: false,
                modal: {
                    plan: false,
                    background: false
                },
                modalFamilia: {
                    name: '',
                    precio: '',
                    modal: '',
                    precioOferta: ''
                },
                resumen: {
                    open: false
                },
                loading: true,
                calling: false,
                error_solicitud: false,
                horariosAgendar: solicitudInfo.horariosAgen,
                params_request: {
                    telefoContacto: '',
                    rut: '',
                    mail: '',
                    captcha: '',
                    codigo: '',
                    horarioAgendar: '',
                    tipoOferta: '',
                    planFamilia: '',
                    utm: solicitudInfo.utm,
                    utms: solicitudInfo.utms
                },
                horarioAgendamiento: '',
                horariosAgendar: solicitudInfo.horariosAgen,
                idLead: '',
                printProduct: '',
                prefijoVirtualUrl: ''
            },
            components: {
                'vue-recaptcha': VueRecaptcha
            },
            mounted: function() {
                this.loading = false;
                horarioActivo = this.horariosAgendar.hours[this.horariosAgendar.active];
                this.horarioAgendamiento = horarioActivo.entrada + '+' + horarioActivo.salida;
                this.params_request.horarioAgendar = horarioActivo.entrada + '+' + horarioActivo.salida;
                self = this;
                $('.formulario-cuadro').removeClass('formulario-cuadro-active');
                this.printProduct = this.getPrintProduct();
                this.prefijoVirtualUrl = this.getPrefijoVirtualUrl();
            },
            methods: {
                /**
                 * [validateFormSol]
                 * Validación del formulario de solicitud.
                 */
                validateFormSol: function() {

                    grecaptcha.reset();

                    formValid = validate.checkForm('form-solicitud');
                    self.error_solicitud = false;
                    if (formValid) {
                        this.calling = true;
                        this.$refs.invisibleRecaptcha.execute();
                    }

                },
                /**
                 * [buildData]
                 * @param  {[type]} token [description]
                 * Construye el params_request con los datos obtenidos del formulario y el token recibido por el recaptcha para su verificación
                 */
                buildData: function(token) {

                    this.params_request.captcha = token;
                    this.params_request.horarioAgendar = this.horarioAgendamiento;
                    this.params_request.tipoOferta = this.general.tipoOferta;
                    this.params_request.rut = $.formatRut($('input[name="rut"]').val(), false);


                    switch (this.general.tipoOferta) {
                        case "1":
                            this.params_request.codigo = this.plan.codigo;
                            break;
                        case "2":
                            this.params_request.codigo = this.offer.codigo;
                            console.log("aqui");
                            break;
                        case "3":
                            this.params_request.planFamilia = this.planesFamilia;
                            break;

                    }

                    if (this.horario) {
                        this.clicktoCall();
                    } else {
                        this.callMeBack();
                    }

                },

                /**
                 * [onVerify]
                 * @param  {[type]} token [variable que retorna la valdación de recaptcha]
                 */
                onVerify: function(token) {
                    this.buildData(token);
                    console.log(token);

                },
                //get prefijo virtual url
                getPrefijoVirtualUrl: function() {
                    var label;
                    switch (self.general.tipoOferta) {
                        case '1':
                            ofertaLabel = "planes";
                            if (self.offer.tipoSolicitud == 3) {
                                modalidaLabel = "portabilidad";
                            } else {
                                modalidaLabel = "nuevalinea";
                            }
                            break;
                        case '2':
                            ofertaLabel = "equipomasplan";
                            if (self.offer.tipoSolicitud == 3) {
                                modalidaLabel = "portalinea";
                            } else if (self.offer.tipoSolicitud == 1) {
                                modalidaLabel = "nuevalinea";
                            }
                            break;
                        case '3':
                            ofertaLabel = "planesfamilia";
                            modalidaLabel = "nuevalinea";
                            break;
                    }
                    return 'movil/' + ofertaLabel + '/' + modalidaLabel + '/';
                },

                getPrintProduct: function() {
                    switch (self.general.tipoOferta) {
                        case '1':
                            products.push({
                                'name': self.plan.name,
                                'id': self.plan.codigo,
                                'category': 'Post Pago',
                                'brand': 'Movistar',
                                'quantity': 1,
                                'price': self.plan.precio,
                                'dimension32': 'Cambiate'
                            });
                            break;
                        case '2':
                            //tag data tipo solicitud
                            var tagData = {
                                tipoSolicitud: '-',
                                tipoPago: '-',
                                tipoPagoInicial: '-'
                            };

                            //Tipo de Solicitud
                            if (self.offer.tipoSolicitud == 1) {
                                tagData.tipoSolicitud = 'Portabilidad';
                            } else if (self.offer.tipoSolicitud == 3) {
                                tagData.tipoSolicitud = 'Linea Nueva';
                            } else {
                                tagData.tipoSolicitud = '-';
                            }

                            //Tipo de metodo de Pago
                            if (self.offer.metodoPago == 1) {
                                tagData.tipoPago = 'Pago Externo';
                                tagData.tipoPagoInicial = 'Sin_PieInicial';
                            } else if (self.offer.metodoPago == 2) {
                                tagData.tipoPago = 'Contra Boleta';
                                tagData.tipoPagoInicial = 'Con_PieInicial';
                            } else if (self.offer.metodoPago == 3) {
                                tagData.tipoPago = 'Movistar One';
                                tagData.tipoPagoInicial = 'Con_PieInicial';
                            } else if (self.offer.metodoPago == 4) {
                                tagData.tipoPago = 'WiFi';
                                tagData.tipoPagoInicial = 'Sin_PieInicial';
                            }

                            //Print del producto al cargar la pagina
                            products.push({
                                'name': self.equipoPadre.name,
                                'id': self.equipoPadre.id,
                                'category': 'Equipo + Plan Multimedia/' + tagData.tipoSolicitud,
                                'brand': self.equipoPadre.marca,
                                'price': self.offer.precioTotal,
                                'quantity': 1,
                                'dimension33': tagData.tipoSolicitud,
                                'dimension34': tagData.tipoPagoInicial,
                                'dimension35': (self.offer.movistarOne ? 'Con_MovistarOne' : 'SinMovistarOne'),
                                'dimension36': self.equipo.id,
                                'variant': self.equipo.nombreColor
                            });
                            break;
                        case '3':
                            jQuery.each(self.planesFamilia.planes, function() {
                                products.push({
                                    'name': this.name,
                                    'id': this.codigo,
                                    'category': 'Post Pago',
                                    'brand': 'Movistar',
                                    'quantity': this.total,
                                    'price': this.precio,
                                    'dimension32': 'Cambiate'
                                });
                            });

                            products.push({
                                'name': self.planesFamilia.planTotal.name,
                                'id': self.planesFamilia.planTotal.codigo,
                                'category': 'Post Pago',
                                'brand': 'Movistar',
                                'quantity': 1,
                                'price': self.planesFamilia.planTotal.precio,
                                'dimension32': 'Cambiate'
                            });
                            break;
                    }
                },

                pushProduct: function(idLead) {

                    switch (self.general.tipoOferta) {
                        case '1':
                            dataLayer.push({
                                'event': 'ecommerce.js',
                                'ecommerce': {
                                    'purchase': {
                                        'actionField': {
                                            'id': idLead,
                                            'revenue': '1',
                                            'list': 'Planes Móvil'
                                        },
                                        'products': products
                                    }
                                }
                            });
                            break;
                        case '2':

                            dataLayer.push({
                                'event': 'ecommerce.js',
                                'IdCamaleon': solicitudInfo.offer.codigo,
                                'ecommerce': {
                                    'purchase': {
                                        'actionField': {
                                            'affiliation': 'Movistar - Venta No Automatizada Equipo+Plan',
                                            'id': idLead,
                                            'revenue': '1'
                                        },
                                        'products': products
                                    }
                                }
                            });
                            break;
                        case '3':
                            dataLayer.push({
                                'event': 'ecommerce.js',
                                'ecommerce': {
                                    'purchase': {
                                        'actionField': {
                                            'id': idLead,
                                            'revenue': '1',
                                            'list': 'Planes Familia'
                                        },
                                        'products': products
                                    }
                                }
                            });
                            break;
                    }
                },

                /**
                 * [clicktoCall]
                 * Se ejecuta el llamado al clicktoCall con la data generada en la función buildData
                 */
                clicktoCall: function() {
                    var ofertaLabel;
                    var modalidaLabel;

                    $.ajax({
                        url: document.location.origin + "/equipomasplan/genericclicktocall/index",
                        data: self.params_request,
                        type: "POST",
                        beforeSend: function() {
                            self.typeScreen = 'conectando';
                            //evento llamando tagueo
                            tagManagerMovil.pushPageView('movil', self.prefijoVirtualUrl + 'llamando');
                        },
                        success: function(data) {
                            self.horario = data.horario;
                            if (data.status) {
                                self.typeScreen = 'conectado';
                                self.idLead = data.idlead;
                                //Pagina de Exito
                                tagManagerMovil.pushPageView('movil', self.prefijoVirtualUrl + 'exito');
                                //Purchase
                                self.pushProduct(data.idlead);
                            } else {
                                console.log(false);
                                self.typeScreen = 'no_conectado';

                                //Error de solicitud
                                //  tagManager.pushPageView('equipo+plan', 'fallido');
                            }

                        },
                        error: function(e) {
                            console.log("error");
                            self.typeScreen = 'no_conectado';
                            self.error_solicitud = true;
                            //Error de solicitud
                            tagManagerMovil.pushPageView('movil', self.prefijoVirtualUrl + 'fallido');
                        },
                        done: function(d) {
                            self.calling = false;
                        }
                    });
                },
                /**
                 * [callMeBack]
                 * función enncarga de gestionar el agendamiento de llamadas fuera de horario con la data construida en buildData
                 */
                callMeBack: function() {
                    self.error_solicitud = false;

                    $.ajax({
                        url: document.location.origin + "/equipomasplan/genericcallmeback/index",
                        data: self.params_request,
                        type: "POST",
                        beforeSend: function() {
                            tagManagerMovil.pushPageView('movil', self.prefijoVirtualUrl + 'llamada-agendada');
                            //tagManager.pushPageView('equipo+plan', 'llamada-agendada');
                        },
                        success: function(data) {
                            self.horario = data.horario;
                            console.log(data);
                            if (data.status) {
                                //Agendamiento Exitoso
                                self.typeScreen = 'agendamiento';
                                self.idLead = data.idlead;
                                //evento agendar ok
                                tagManagerMovil.pushPageView('movil', self.prefijoVirtualUrl + 'agendamiento-ok');
                                //push product datalayer
                                self.pushProduct(data.idlead);
                            } else {
                                //Agendamiento Erroneo, intente de nuevo.
                                self.error_solicitud = true;
                                self.horario = data.horario;
                                self.calling = false;
                                self.reintentar = true;
                                //evento error agendar tagueo
                                tagManagerMovil.pushPageView('movil', self.prefijoVirtualUrl + 'agedamiento-error');

                                //  tagManager.pushPageView('equipo+plan', 'agedamiento-error');
                            }
                        },
                        error: function() {
                            self.reintentar = true;
                            self.calling = false;
                            //evento error agendar tagueo
                            tagManagerMovil.pushPageView('movil', self.prefijoVirtualUrl + 'agedamiento-error');
                        },
                        done: function() {
                            //Finaliza el proceso de agendamiento
                            self.calling = false;
                        }

                    });


                },
                modalOpen: function(modal, planFamilia = null) {
                    modal.plan = modal.background = true;

                    if (this.general.tipoOferta == 3) {
                        this.updateModalFamilia(planFamilia);
                    }

                },
                updateModalFamilia: function(plan) {
                    this.modalFamilia.name = plan.name;
                    this.modalFamilia.precio = plan.precio;
                    this.modalFamilia.modal = plan.modal;
                    this.modalFamilia.precioOferta = plan.precioOferta;
                },

                modalClose: function(modal) {
                    modal.plan = modal.background = false;
                },

                toggleResumen: function(resumen) {

                    if (resumen.open) {
                        $('#v1Form .formulario-cuadro .cuadro-right .collapsedMobile').slideToggle(500, function() {
                            resumen.open = !resumen.open;
                        });
                    } else {
                        $('#v1Form .formulario-cuadro .cuadro-right .collapsedMobile').slideToggle(500);
                        resumen.open = !resumen.open;
                    }

                },

                /**
                 * [reloadPageFormulario]
                 *  Se recarga el formulario cuando se logra ejecutar la llamada al cliente [clicktoCall]
                 */
                reloadPageFormulario: function() {
                    this.calling = false;
                    self.error_solicitud = false;
                    this.typeScreen = "FORMULARIO";

                },
                /**
                 * [updateHour]
                 * @param  {[type]} hora [contiene la hora seleccionada para agendamiento]
                 * Se realiza un seteo de la variable horarioAgendar de params_request con el valor seleccionado.
                 */
                updateHour: function(hora) {
                    this.horarioAgendamiento = hora;
                    this.params_request.horarioAgendar = hora;

                },

                clickDropdownAgendar: function() {

                    $('.options_horarios').toggleClass('showOptionAgenda');
                    $('#v1Form .formulario-cuadro .cuadro-left .frm-campos .frm-inputs .horarioAtencion .activeH .flecha span').toggleClass('icono-flecha_down2 icono-flecha_up2');

                    $('.options_horarios li').click(function() {
                        //alert($(this).attr("data-horario"));
                        $('.options_horarios li').removeClass('shHorario');
                        $(this).addClass('shHorario');
                        $('.activeH .HorarioSeleccionado').html($(this).text());
                    });

                },
                //Limpieza de RUT
                cleanRut: function(item) {

                    var regexK = new RegExp('[k]+');
                    var regexClean = new RegExp('[^0-9kK.-]+');
                    var regexMutipleK = new RegExp('K(?=.*K)');

                    var value = item.rut;

                    item.rut = value.replace(regexK, 'K').replace(regexClean, '').replace(regexMutipleK, '');

                    if (item.rut.length > 12) {
                        item.rut = item.rut.substring(0, 12);
                    }

                    item.rut = $.formatRut(item.rut);

                    //return rut;

                },
                //Limpieza de Telefono
                cleanPhone: function(item) {

                    var regexClean = new RegExp('[^0-9]+');
                    var value = item.telefoContacto;

                    item.telefoContacto = value.replace(/[^0-9]/g, '');

                    if (item.telefoContacto.charAt(0) == '0') {
                        item.telefoContacto = item.telefoContacto.substring(1);
                    }

                    if (item.telefoContacto.length > 9) {
                        item.telefoContacto = item.telefoContacto.substring(0, 9);
                    }

                }

            }
        })

        /**
         * función de validación donde se establecen las reglas de validación en los inputs del formulario de agendamiento.
         */
        validate.validateForm({
            'formId': 'form-solicitud',
            'inputs': {


                'phone': {
                    'method': 'Phone',
                    'message': $.mage.__('Error Phone'),
                    'categoryTag': $.mage.__('Error Form Category Tag'),
                    'labelTag': $.mage.__('Error Phone Tag'),
                },
                'rut': {
                    'method': 'Rut',
                    'message': $.mage.__('Error RUT'),
                    'categoryTag': $.mage.__('Error Form Category Tag'),
                    'labelTag': $.mage.__('Error RUT Tag')
                },
                'email': {
                    'method': 'Email',
                    'message': $.mage.__('Error Email'),
                    'categoryTag': $.mage.__('Error Form Category Tag'),
                    'labelTag': $.mage.__('Error Email Tag')
                }
            }
        });

    })