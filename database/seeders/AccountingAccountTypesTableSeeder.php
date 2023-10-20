<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\helpers;

//Models:
use App\Models\AccountingAccountType;

class AccountingAccountTypesTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * Clasificación obtenida del Plan General de Contabilidad: https://www.plangeneralcontable.com/?tit=listado-de-contenidos&name=GeTia&contentId=mod_list&contGroupId=ctg_25
     *
     * @return void
     */
    public function run(){
        $data = [
    1 => [
        'name' => ucfirst(trans('fiscal.financiacion_basica')),
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.capital')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.capital_social')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.fondo_social')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.capital')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.socios_desembolsos_no_exigidos')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.socios_desembolsos_no_exigidos_capital_social'))
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.socios_desembolsos_no_exigidos_pendiente_inscripcion'))
                            ]
                        ]
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.socios_aportaciones_no_dinerarias_pendientes')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.socios_aportaciones_no_dinerarias_pendientes_capital_social'))
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.socios_aportaciones_no_dinerarias_pendientes_pendiente_inscripcion'))
                            ],
                        ]
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_propias_situaciones_especiales')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_propias_reduccion_capital')),
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.reservas')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.prima_emision_asuncion')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.patrimonio_neto_emision_instrumentos_financieros_compuestos')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.patrimonio_neto_emision_instrumentos_financieros_compuestos'))
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.resto_instrumentos_patrimonio_neto'))
                            ]
                        ]
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.reserva_legal')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.reservas_voluntarias')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.reservas_especiales')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.reservas_acciones_participaciones_sociedad_dominante'))
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.reservas_estatutarias'))
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.reserva_capital_amortizado'))
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.reserva_fondo_comercio'))
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.reserva_acciones_propias'))
                            ]
                        ]
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.reserva_perdidas_ganancias_actuariales')),
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.aportaciones_socios_propietarios')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.diferencias_ajuste_capital_euros')),
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.resultado_pendientes_aplicacion')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.remanente')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.resultados_negativos_ejercicios_anteriores')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.resultados_ejercicio')),
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.subvenciones_donaciones_ajustes_cambio_valor')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.subvenciones_oficiales_capital')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.donaciones_legados_capital')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.otras_subvenciones_donaciones_legados')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.ajustes_valoracion_instrumentos_financieros')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.operaciones_cobertura')),
                        'level4' => [
                            '0' => ['name' => ucfirst(trans('fiscal.cobertura_flujos_efectivo'))],
                            '1' => ['name' => ucfirst(trans('fiscal.cobertura_inversion_neta_negocio_extranjero'))]
                        ]
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.diferencias_conversion')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.ajustes_valoracion_activos_no_corrientes')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.ingresos_fiscales_varios_ejercicios')),
                        'level4' => [
                            '0' => ['name' => ucfirst(trans('fiscal.ingresos_fiscales_diferencias_permanentes'))],
                            '1' => ['name' => ucfirst(trans('fiscal.ingresos_fiscales_deducciones_bonificaciones'))]
                        ]
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.provisiones')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.provision_retribuciones')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.provision_impuestos')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.provision_responsabilidades')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.provision_desmantelamiento')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.provision_actuaciones')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.provision_reestructuraciones')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.provisiones_transacciones')),
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.deudas_largo_plazo')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.acciones_largo_plazo_pasivo')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_no_exigidos_participaciones_pasivo')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.aportaciones_participaciones_pasivo')),
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.deudas_largo_plazo_vinculadas')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudas_largo_plazo_entidades_vinculadas')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.proveedores_inmovilizado_largo_plazo_vinculadas')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.acreedores_arrendamiento_financiero_largo_plazo_vinculadas')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.otras_deudas_largo_plazo_vinculadas')),
                        'level4' => [
                            '3' => ['name' => ucfirst(trans('fiscal.otras_deudas_grupo'))],
                            '4' => ['name' => ucfirst(trans('fiscal.otras_deudas_asociadas'))],
                            '5' => ['name' => ucfirst(trans('fiscal.otras_deudas_vinculadas'))]
                        ]
                    ]
                ]
            ],
            '7' => [
                'name' => ucfirst(trans('fiscal.deudas_largo_plazo_recibidos')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudas_entidades_credito')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deudas_a_largo_plazo')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.deudas_transformables_subvenciones')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.proveedores_inmovilizado_largo_plazo')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.acreedores_arrendamiento_financiero_largo_plazo')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.efectos_pagar_largo_plazo')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.pasivos_derivados_financieros_largo_plazo')),
                        'level4' => [
                            '5' => ['name' => ucfirst(trans('fiscal.pasivos_derivados_financieros_largo_plazo_cartera_negociación'))],
                            '8' => ['name' => ucfirst(trans('fiscal.pasivos_derivados_financieros_largo_plazo_instrumentos_cobertura'))]
                        ]
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.obligaciones_bonos')),
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.obligaciones_bonos_convertibles')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.deudas_representadas_valores_negociables')),
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.fianzas_depositos_largo_plazo')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.fianzas_largo_plazo')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.anticipos_ventas_prestaciones_servicios_largo_plazo')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.depositos_largo_plazo')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.garantias_financieras_largo_plazo')),
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.situaciones_transitorias_financiacion')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_emitidas')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.suscriptores_acciones')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.capital_pendiente_inscripcion')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_pasivos_financieros')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.suscriptores_pasivos_financieros')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.acciones_pasivos_financieros_pendientes_inscripcion')),
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    2 => [
        'name' => ucfirst(trans('fiscal.inmovilizado')),
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.inmovilizaciones_intangibles')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.gastos_investigacion')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.desarrollo')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.concesiones_administrativas')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.propiedad_industrial')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.fondo_comercio')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.derechos_traspaso')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.aplicaciones_informaticas')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.anticipos_inmovilizaciones_intangibles')),
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.inmovilizaciones_materiales')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.terrenos_bienes_naturales')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.construcciones')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.instalaciones_tecnicas')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.maquinaria')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.utillaje')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.otras_instalaciones')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.mobiliario')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.equipos_procesos_informacion')),
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.elementos_transporte')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otro_inmovilizado_material')),
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.inversiones_inmobiliarias')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.inversiones_terrenos_bienes_naturales')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.inversiones_construcciones')),
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.inmovilizaciones_materiales_curso')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.adaptacion_terrenos_bienes_naturales')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.construcciones_curso')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.instalaciones_tecnicas_montaje')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.maquinaria_montaje')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.equipos_procesos_informacion_montaje')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.anticipos_inmovilizaciones_materiales')),
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.inversiones_financieras_largo_plazo_vinculadas')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.participaciones_largo_plazo_vinculadas')),
                        'level4' => [
                            '3' => [
                                'name' => ucfirst(trans('fiscal.participacion_largo_plazo_empresas_grupo'))
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.participacion_largo_plazo_empresas_asociadas'))
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.participaciones_largo_plazo_otras_vinculadas'))
                            ]
                        ]
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.valores_representativos_largo_plazo_vinculadas')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo_partes_vinculadas')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_pendientes_participaciones_largo_plazo_vinculadas')),
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.otras_inversiones_financieras_largo_plazo')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.inversiones_financieras_largo_plazo_instrumentos_patrimonio')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.valores_representativos_largo_plazo')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo_enajenacion_inmovilizado')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo_personal')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.activos_derivados_financieros_largo_plazo')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.activos_financieros_largo_plazo_negociacion'))
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.activos_financieros_largo_plazo_cobertura'))
                            ]
                        ]
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.activos_retribuciones_largo_definida')),
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.imposiciones_largo_plazo')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_pendientes_neto_largo')),
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.fianzas_depositos_largo')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.fianzas_largo')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.depositos_largo')),
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.amortizacion_acumulada_inmovilizado')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_intangible')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_material')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_inmobiliarias')),
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.deterioro_no_corrientes')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deterioro_intangible')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deterioro_material')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.deterioro_inmobiliarias')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.deterioro_largo_vinculadas')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.deterioro_deuda_largo_vinculadas')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.deterioro_creditos_largo_vinculadas')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deterioro_neto_largo')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.deterioro_deuda_largo')),
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.deterioro_valor_largo')),
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    3 => [
        'name' => ucfirst(trans('fiscal.existencias')),
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.comerciales')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.comerciales')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.comerciales')),
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.materias_primas')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.materias_primas')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.materias_primas')),
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.otros_aprovisionamientos')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.elementos_conjuntos_incorporables')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.combustibles')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.repuestos')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.materiales_diversos')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.embalajes')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.envases')),
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.material_oficina')),
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.productos_curso')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.productos_curso')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.productos_curso')),
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.productos_semiterminados')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.productos_semiterminados')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.productos_semiterminados')),
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.productos_terminados')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.productos_terminados')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.productos_terminados')),
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.subproductos_residuos_recuperados')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.subproductos_residuos_recuperados')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.subproductos_residuos_recuperados')),
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.deterioro_existencias')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deterioro_mercaderias')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deterioro_primas')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.deterioro_aprovisionamientos')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.deterioro_productos_curso')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.deterioro_semiterminados')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.deterioro_terminados')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deterioro_subproductos_residuos_recuperados')),
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    4 => [
        'name' => ucfirst(trans('fiscal.acreedores_deudores')),
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.proveedores')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.proveedores')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.proveedores_comerciales_pagar')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.proveedores_empresas_grupo')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.proveedores_asociadas')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.proveedores_vinculadas')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.envases_proveedores')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.anticipos_proveedores')),
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.acreedores_varios')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.acreedores_servicios')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.acreedores_comerciales_pagar')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.acreedores_operaciones_comun')),
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.clientes')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.clientes')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.clientes_comerciales_cobrar')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.clientes_factoring')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.clientes_empresas_grupo')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.clientes_asociadas')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.clientes_vinculadas')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.clientes_dudoso_cobro')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.envases_devolver_clientes')),
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.anticipos_clientes')),
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.deudores_varios')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudores')),
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deudores_comerciales_cobrar')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deudores_cobro')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.deudores_comun')),
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.personal')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.anticipos_remuneraciones')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.remuneraciones_pendientes')),
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.remuneraciones_sistemas_definida_pendientes')),
                        'level4' => false
                    ]
                ]
            ],
            '7' => [
                'name' => ucfirst(trans('fiscal.administraciones_publicas')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.publica_deudora_conceptos')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.publica_iva'))
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.publica_subvenciones_concedidas'))
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.publica_devolucion_impuestos'))
                            ]
                        ]
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.organismos_social_deudores')),
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.publica_iva_soportado')),
                        'level4' => false
                    ],
                    '3' => [
                        'name'=> ucfirst(trans('fiscal.publica_retenciones_cuenta')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.activos_impuesto_diferido')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.activos_temporarias_deducibles'))
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.derechos_deducciones_bonificaciones_pendientes'))
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.credito_perdidas_ejercicio'))
                            ]
                        ]
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.publica_fiscales')),
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.publica_acreedora_iva'))
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.publica_practicadas'))
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.publica_impuesto_sociedades'))
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.publica_subvenciones_reintegrar'))
                            ]
                        ]
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.organismos_social_acreedores')),
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.publica_iva_repercutido')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.pasivos_temporarias_imponibles')),
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.ajustes_periodificacion')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.gastos_anticipados')),
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.ingresos_anticipados')),
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.deterioro_creditos_corto')),
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deterioro_comerciales')),
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.deterioro_comerciales_vinculadas')),
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.provisiones_comerciales')),
                        'level4' => [
                            '4' => [
                                'name' => ucfirst(trans('fiscal.provision_onerosos'))
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.provision_comerciales'))
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    5 => [
        'name' => ucfirst(trans('fiscal.cuentas_financieras')),
        'level2' =>  [

        ]
    ],
    6 => [
        'name' => ucfirst(trans('fiscal.compras_gastos')),
        'level2' =>  [

        ]
    ],
    7 => [
        'name' => ucfirst(trans('fiscal.ventas_ingresos')),
        'level2' =>  [

        ]
    ],
    8 => [
        'name' => ucfirst(trans('fiscal.gastos_patrimonio_neto')),
        'level2' =>  [

        ]
    ],
    9 => [
        'name' => ucfirst(trans('fiscal.ingresos_patrimonio_neto')),
        'level2' =>  [

        ]
    ]
];

        foreach($data as $i1 => $r1){
            $aat1 = new AccountingAccountType();
            $aat1->autoreference = '0';
            $aat1->code = $i1;
            $aat1->name = $r1['name'];
            $aat1->save();

            if(isset($r1['level2'])){
                foreach($r1['level2'] as $i2 => $r2){
                    $aat2 = new AccountingAccountType();
                    $aat2->autoreference = $aat1->id;
                    $aat2->code = $i1.$i2;
                    $aat2->name = $r2['name'];
                    $aat2->save();

                    if(isset($r2['level3'])){
                        foreach($r2['level3'] as $i3 => $r3){
                            $aat3 = new AccountingAccountType();
                            $aat3->autoreference = $aat2->id;
                            $aat3->code = $i1.$i2.$i3;
                            $aat3->name = $r3['name'];
                            $aat3->save();

                            if($r3['level4']){
                                foreach($r3['level4'] as $i4 => $r4){
                                    $aat4 = new AccountingAccountType();
                                    $aat4->autoreference = $aat3->id;
                                    $aat4->code = $i1.$i2.$i3.$i4;
                                    $aat4->name = $r4['name'];
                                    $aat4->save();
                                }    
                            }
                        }
                    }
                }
            }
        }
    }
}
