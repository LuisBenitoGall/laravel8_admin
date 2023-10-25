@extends('admin.partials.layout')

@section('content')


@php
$data = [
    1 => [
        'name' => ucfirst(trans('fiscal.financiacion_basica')),
        'group' => '',
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.capital')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.capital_social')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.fondo_social')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.capital')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.socios_desembolsos_no_exigidos')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.socios_desembolsos_no_exigidos_capital_social')),
                                'group' => ''
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.socios_desembolsos_no_exigidos_pendiente_inscripcion')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.socios_aportaciones_no_dinerarias_pendientes')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.socios_aportaciones_no_dinerarias_pendientes_capital_social')),
                                'group' => ''
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.socios_aportaciones_no_dinerarias_pendientes_pendiente_inscripcion')),
                                'group' => ''
                            ],
                        ]
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_propias_situaciones_especiales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_propias_reduccion_capital')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.reservas')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.prima_emision_asuncion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.patrimonio_neto_emision_instrumentos_financieros_compuestos')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.patrimonio_neto_emision_instrumentos_financieros_compuestos')),
                                'group' => ''
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.resto_instrumentos_patrimonio_neto')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.reserva_legal')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.reservas_voluntarias')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.reservas_especiales')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.reservas_acciones_participaciones_sociedad_dominante')),
                                'group' => ''
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.reservas_estatutarias')),
                                'group' => ''
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.reserva_capital_amortizado')),
                                'group' => ''
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.reserva_fondo_comercio')),
                                'group' => ''
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.reserva_acciones_propias')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.reserva_perdidas_ganancias_actuariales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.aportaciones_socios_propietarios')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.diferencias_ajuste_capital_euros')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.resultado_pendientes_aplicacion')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.remanente')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.resultados_negativos_ejercicios_anteriores')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.resultados_ejercicio')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.subvenciones_donaciones_ajustes_cambio_valor')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.subvenciones_oficiales_capital')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.donaciones_legados_capital')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.otras_subvenciones_donaciones_legados')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.ajustes_valoracion_instrumentos_financieros')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.operaciones_cobertura')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.cobertura_flujos_efectivo')),'group' => ''
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.cobertura_inversion_neta_negocio_extranjero')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.diferencias_conversion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.ajustes_valoracion_activos_no_corrientes')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.ingresos_fiscales_varios_ejercicios')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.ingresos_fiscales_diferencias_permanentes')),
                                'group' => ''
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.ingresos_fiscales_deducciones_bonificaciones')),
                                'group' => ''
                            ],
                        ]
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.provisiones')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.provision_retribuciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.provision_impuestos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.provision_responsabilidades')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.provision_desmantelamiento')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.provision_actuaciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.provision_reestructuraciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.provisiones_transacciones')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.deudas_largo_plazo')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.acciones_largo_plazo_pasivo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_no_exigidos_participaciones_pasivo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.aportaciones_participaciones_pasivo')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.deudas_largo_plazo_vinculadas')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudas_largo_plazo_entidades_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.proveedores_inmovilizado_largo_plazo_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.acreedores_arrendamiento_financiero_largo_plazo_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.otras_deudas_largo_plazo_vinculadas')),
                        'group' => '',
                        'level4' => [
                            '3' => [
                                'name' => ucfirst(trans('fiscal.otras_deudas_grupo')),
                                'group' => ''
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.otras_deudas_asociadas')),
                                'group' => ''
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.otras_deudas_vinculadas')),
                                'group' => ''
                            ]
                        ]
                    ]
                ]
            ],
            '7' => [
                'name' => ucfirst(trans('fiscal.deudas_largo_plazo_recibidos')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudas_entidades_credito')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deudas_a_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.deudas_transformables_subvenciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.proveedores_inmovilizado_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.acreedores_arrendamiento_financiero_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.efectos_pagar_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.pasivos_derivados_financieros_largo_plazo')),
                        'group' => '',
                        'level4' => [
                            '5' => [
                                'name' => ucfirst(trans('fiscal.pasivos_derivados_financieros_largo_plazo_cartera_negociación')),
                                'group' => ''
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.pasivos_derivados_financieros_largo_plazo_instrumentos_cobertura')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.obligaciones_bonos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.obligaciones_bonos_convertibles')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.deudas_representadas_valores_negociables')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.fianzas_depositos_largo_plazo')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.fianzas_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.anticipos_ventas_prestaciones_servicios_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.depositos_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.garantias_financieras_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.situaciones_transitorias_financiacion')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_emitidas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.suscriptores_acciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.capital_pendiente_inscripcion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.acciones_participaciones_pasivos_financieros')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.suscriptores_pasivos_financieros')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.acciones_pasivos_financieros_pendientes_inscripcion')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ]
        ],
        'group' => ''
    ],
    2 => [
        'name' => ucfirst(trans('fiscal.inmovilizado')),
        'group' => '',
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.inmovilizaciones_intangibles')),
                'group' => 'a',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.gastos_investigacion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.desarrollo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.concesiones_administrativas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.propiedad_industrial')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.fondo_comercio')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.derechos_traspaso')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.aplicaciones_informaticas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.anticipos_inmovilizaciones_intangibles')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.inmovilizaciones_materiales')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.terrenos_bienes_naturales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.construcciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.instalaciones_tecnicas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.maquinaria')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.utillaje')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.otras_instalaciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.mobiliario')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.equipos_procesos_informacion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.elementos_transporte')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otro_inmovilizado_material')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.inversiones_inmobiliarias')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.inversiones_terrenos_bienes_naturales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.inversiones_construcciones')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.inmovilizaciones_materiales_curso')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.adaptacion_terrenos_bienes_naturales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.construcciones_curso')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.instalaciones_tecnicas_montaje')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.maquinaria_montaje')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.equipos_procesos_informacion_montaje')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.anticipos_inmovilizaciones_materiales')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.inversiones_financieras_largo_plazo_vinculadas')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.participaciones_largo_plazo_vinculadas')),
                        'group' => '',
                        'level4' => [
                            '3' => [
                                'name' => ucfirst(trans('fiscal.participacion_largo_plazo_empresas_grupo')),
                                'group' => ''
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.participacion_largo_plazo_empresas_asociadas')),
                                'group' => ''
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.participaciones_largo_plazo_otras_vinculadas')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.valores_representativos_largo_plazo_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo_partes_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_pendientes_participaciones_largo_plazo_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.otras_inversiones_financieras_largo_plazo')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.inversiones_financieras_largo_plazo_instrumentos_patrimonio')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.valores_representativos_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo_enajenacion_inmovilizado')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.creditos_largo_plazo_personal')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.activos_derivados_financieros_largo_plazo')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.activos_financieros_largo_plazo_negociacion')),
                                'group' => ''
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.activos_financieros_largo_plazo_cobertura')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.activos_retribuciones_largo_definida')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.imposiciones_largo_plazo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_pendientes_neto_largo')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.fianzas_depositos_largo')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.fianzas_largo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.depositos_largo')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.amortizacion_acumulada_inmovilizado')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_intangible')),
                        'group' => 'a',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_material')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_inmobiliarias')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.deterioro_no_corrientes')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deterioro_intangible')),
                        'group' => 'a',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deterioro_material')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.deterioro_inmobiliarias')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.deterioro_largo_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.deterioro_deuda_largo_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.deterioro_creditos_largo_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deterioro_neto_largo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.deterioro_deuda_largo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.deterioro_valor_largo')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    3 => [
        'name' => ucfirst(trans('fiscal.existencias')),
        'group' => '',
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.comerciales')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.comerciales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.comerciales')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.materias_primas')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.materias_primas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.materias_primas')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.otros_aprovisionamientos')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.elementos_conjuntos_incorporables')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.combustibles')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.repuestos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.materiales_diversos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.embalajes')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.envases')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.material_oficina')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.productos_curso')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.productos_curso')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.productos_curso')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.productos_semiterminados')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.productos_semiterminados')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.productos_semiterminados')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.productos_terminados')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.productos_terminados')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.productos_terminados')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.subproductos_residuos_recuperados')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.subproductos_residuos_recuperados')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.subproductos_residuos_recuperados')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.deterioro_existencias')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deterioro_mercaderias')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deterioro_primas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.deterioro_aprovisionamientos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.deterioro_productos_curso')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.deterioro_semiterminados')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.deterioro_terminados')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deterioro_subproductos_residuos_recuperados')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    4 => [
        'name' => ucfirst(trans('fiscal.acreedores_deudores')),
        'group' => '',
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.proveedores')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.proveedores')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.proveedores_comerciales_pagar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.proveedores_empresas_grupo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.proveedores_asociadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.proveedores_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.envases_proveedores')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.anticipos_proveedores')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.acreedores_varios')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.acreedores_servicios')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.acreedores_comerciales_pagar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.acreedores_operaciones_comun')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.clientes')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.clientes')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.clientes_comerciales_cobrar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.clientes_factoring')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.clientes_empresas_grupo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.clientes_asociadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.clientes_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.clientes_dudoso_cobro')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.envases_devolver_clientes')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.anticipos_clientes')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.deudores_varios')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudores')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deudores_comerciales_cobrar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deudores_cobro')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.deudores_comun')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.personal')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.anticipos_remuneraciones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.remuneraciones_pendientes')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.remuneraciones_sistemas_definida_pendientes')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '7' => [
                'name' => ucfirst(trans('fiscal.administraciones_publicas')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.publica_deudora_conceptos')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.publica_iva')),
                                'group' => ''
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.publica_subvenciones_concedidas')),
                                'group' => ''
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.publica_devolucion_impuestos')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.organismos_social_deudores')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.publica_iva_soportado')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name'=> ucfirst(trans('fiscal.publica_retenciones_cuenta')),
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.activos_impuesto_diferido')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.activos_temporarias_deducibles')),
                                'group' => ''
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.derechos_deducciones_bonificaciones_pendientes')),
                                'group' => ''
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.credito_perdidas_ejercicio')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.publica_fiscales')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.publica_acreedora_iva')),
                                'group' => ''
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.publica_practicadas')),
                                'group' => ''
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.publica_impuesto_sociedades')),
                                'group' => ''
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.publica_subvenciones_reintegrar')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.organismos_social_acreedores')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.publica_iva_repercutido')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.pasivos_temporarias_imponibles')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.ajustes_periodificacion')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.gastos_anticipados')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.ingresos_anticipados')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.deterioro_creditos_corto')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deterioro_comerciales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.deterioro_comerciales_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.provisiones_comerciales')),
                        'group' => '',
                        'level4' => [
                            '4' => [
                                'name' => ucfirst(trans('fiscal.provision_onerosos')),
                                'group' => ''
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.provision_comerciales')),
                                'group' => ''
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    5 => [
        'name' => ucfirst(trans('fiscal.cuentas_financieras')),
        'group' => '',
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.emprestitos_especiales_analogas_corto')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.obligaciones_bonos_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.obligaciones_bonos_convertibles_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.acciones_corto_pasivo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.deudas_negociables_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.intereses_analogas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.dividendos_emisiones_pasivo')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.valores_amortizados')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.deudas_corto_vinculadas')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudas_corto_credito_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.proveedores_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.acreedores_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.otras_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.intereses_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.deudas_corto_recibidos')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.deudas_corto_credito')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.prestamos_corto_credito')),
                                'group' => ''
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.deudas_corto_dispuesto')),
                                'group' => ''
                            ],
                            '7' => [
                                'name' => ucfirst(trans('fiscal.deudas_operaciones_confirming')),
                                'group' => ''
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.deudas_efectos_descontados')),
                                'group' => ''
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.deudas_operaciones_factoring')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.deudas_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.deudas_corto_transformables')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' =>  [
                        'name' => ucfirst(trans('fiscal.proveedores_inmovilizado_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.acreedores_financiero_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.efectos_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.dividendo_pagar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.intereses_corto_credito')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.intereses_corto_deudas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.provisiones_corto')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.inversiones_corto_vinculadas')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.participaciones_corto_vinculadas')),
                        'group' => '',
                        'level4' => [
                            '3' => [
                                'name' => ucfirst(trans('fiscal.participaciones_corto_grupo')),
                                'group' => ''
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.participaciones_corto_asociadas')),
                                'group' => ''
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.participaciones_corto_otras_vinculadas')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.valores_deuda_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.creditos_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.intereses_corto_financieras_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.intereses_corto_creditos_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.dividendo_cobrar_inversiones_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_participaciones_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.otras_inversiones_corto')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.inversiones_temporales_patrimonio')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.valores_deuda_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.creditos_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.creditos_corto_enajenacion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.creditos_corto_personal')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.dividendo_cobrar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.intereses_corto_representativos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.intereses_corto_creditos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.imposiciones_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_patrimonio_corto')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.otras_cuentas_no_bancarias')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.titular_explotacion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.cuenta_socios_administradores')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.cuenta_otras_entidades')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.cuentas_fusiones')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.socios_sociedad_disuelta')),
                                'group' => ''
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.socios_fusion')),
                                'group' => ''
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.socios_escindida')),
                                'group' => ''
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.socios_escision')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.cuenta_temporales_bienes')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.partidas_aplicacion')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.desembolsos_participaciones_neto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.dividendo_cuenta')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.socios_desembolsos_exigidos')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.socios_exigidos_ordinarias')),
                                'group' => ''
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.socios_exigidos_pasivos')),
                                'group' => ''
                            ]
                        ]
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.derivados_corto')),
                        'group' => '',
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.activos_corto_negociacion')),
                                'group' => ''
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.activos_corto_cobertura')),
                                'group' => ''
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.pasivos_corto_negociacion')),
                                'group' => ''
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.pasivos_corto_cobertura')),
                                'group' => ''
                            ]
                        ]
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.fianzas_recibidos_corto')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.fianzas_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.depositos_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.fianzas_constituidas_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.depositos_constituidos_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.intereses_anticipado')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.intereses_cobrados_anticipado')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.garantias_corto')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '7' => [
                'name' => ucfirst(trans('fiscal.tesoreria')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.caja_euros')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.caja_extranjera')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.bancos_credito_euros')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.bancos_credito_extranjera')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.bancos_ahorro_euros')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.bancos_ahorro_extranjera')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.inversiones_corto_liquidez')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.activos_mantenidos_venta_asociados')),
                'group' => '',
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.inmovilizado')),
                        'group' => '',
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.inversiones_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.inversiones_financieras')),
                        'group' => '',
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.existencias_comerciales_cobrar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.otros_activos')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.provisiones')),
                        'group' => '',
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deudas_especiales')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.deudas_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.acreedores_cuentas_pagar')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otros_pasivos')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.deterioro_inversiones_corto_venta')),
                'group' => '',
                'level3' => [
                    '3' => [
                        'name' => ucfirst(trans('fiscal.deterioro_participaciones_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.deterioro_valores_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.deterioro_creditos_corto_vinculadas')),
                        'group' => '',
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.deterioro_valores_deuda_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.deterioro_valor_creditos_corto')),
                        'group' => '',
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.deterioro_activos_venta')),
                        'group' => '',
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    6 => [
        'name' => ucfirst(trans('fiscal.compras_gastos')),
        'group' => false,
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.compras')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.compras_mercaderias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.compras_primas')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.compras_aprovisionamientos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.descuentos_compras_pronto')),
                        'group' => false,
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.trabajos_empresas')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.devoluciones_compras_similares')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.rappels_compras')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.variacion_existencias')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.variacion_mercaderias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.variacion_primas')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.variacion_aprovisionamientos')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '2' => [
                'name' => ucfirst(trans('fiscal.servicios_exteriores')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.gastos_ejercicio')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.arrendamientos_canones')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.reparaciones_conservacion')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.servicios_independientes')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.transportes')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.primas_seguros')),
                        'group' => false,
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.servicios_similares')),
                        'group' => false,
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.publicidad_publicas')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.suministros')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otros_servicios')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.tributos')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.impuesto_beneficios')),
                        'group' => false,
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.impuesto_corriente')),
                                'group' => false
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.impuesto_diferido')),
                                'group' => false
                            ]
                        ]
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.otros_tributos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.ajustes_beneficios')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.ajustes_indirecta')),
                        'group' => false,
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.devolucion_impuestos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.ajustes_positivos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.ajustes_positivos_indirecta')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.gastos_personal')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.sueldos_salarios')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.indemnizaciones')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.seguridad_empresa')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.retribuciones_largo_definida')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.retribuciones_prestacion')),
                        'group' => false,
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.contribuciones_anuales')),
                                'group' => false
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.otros_costes')),
                                'group' => false
                            ]
                        ]
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.retribuciones_personal_patrimonio')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otros_sociales')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.otros_gestion')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.perdidas_incobrables')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.resultados_comun')),
                        'group' => false,
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.beneficio_gestor')),
                                'group' => false
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.perdida_participe')),
                                'group' => false
                            ]
                        ]
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otras_corriente')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.gastos_financieros')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.gastos_provisiones')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.intereses_bonos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.intereses_deudas')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.perdidas_instrumentos_razonable')),
                        'group' => false,
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.perdidas_negociacion')),
                                'group' => false
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.perdidas_empresa')),
                                'group' => false
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.perdidas_venta')),
                                'group' => false
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.perdidas_cobertura')),
                                'group' => false
                            ]
                        ]
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.dividendos_pasivo')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.intereses_factoring')),
                        'group' => false,
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.perdidas_deuda')),
                        'group' => false,
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.perdidas_comerciales')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.diferencias_cambio')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otros_financieros')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '7' => [
                'name' => ucfirst(trans('fiscal.perdidas_excepcionales')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.perdidas_intangible')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.perdidas_material')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.perdidas_inmobiliarias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.perdidas_vinculadas')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.perdidas_propias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.gastos_excepcionales')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '8' => [
                'name' => ucfirst(trans('fiscal.dotaciones_amortizaciones')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_inmovilizado_intangible')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_inmovilizado_material')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.amortizacion_inversiones_inmobiliarias')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.perdidas_dotaciones')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.perdidas_deterioro_intangible')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.perdidas_deterioro_material')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.perdidas_deterioro_inmobiliarias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.perdidas_deterioro_existencias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.perdidas_deterioro_comerciales')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.dotacion_comerciales')),
                        'group' => false,
                        'level4' => [
                            '4' => [
                                'name' => ucfirst(trans('fiscal.dotacion_contratos_onerosos')),
                                'group' => false
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.dotacion_otras_comerciales')),
                                'group' => false
                            ]
                        ]
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.perdidas_valores_largo')),
                        'group' => false,
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.perdidas_largo')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.perdidas_corto')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.perdidas_creditos_corto')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    7 => [
        'name' => ucfirst(trans('fiscal.ventas_ingresos')),
        'group' => false,
        'level2' =>  [
            '0' => [
                'name' => ucfirst(trans('fiscal.ventas_propia_servicios')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.ventas_mercaderias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.ventas_terminados')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.ventas_semiterminados')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.ventas_residuos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.ventas_embalajes')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.prestacion_servicios')),
                        'group' => false,
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.descuentos_pronto')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.devoluciones_similares')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.rappels_ventas')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '1' => [
                'name' => ucfirst(trans('fiscal.variacion')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.variacion_curso')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.variacion_semiterminados')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.variacion_terminados')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.variacion_recuperados')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '3' => [
                'name' => ucfirst(trans('fiscal.trabajos_realizados_empresa')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.trabajos_intangible')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.trabajos_material')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.trabajos_inmobiliarias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.trabajos_curso')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '4' => [
                'name' => ucfirst(trans('fiscal.subvenciones_legados')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.subvenciones_explotacion')),
                        'group' => false,
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.subvenciones_ejercicio')),
                        'group' => false,
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.otras_ejercicio')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '5' => [
                'name' => ucfirst(trans('fiscal.otros_ingresos_gestion')),
                'group' => false,
                'level3' => [
                    '1' => [
                        'name' => ucfirst(trans('fiscal.resultados_operaciones_comun')),
                        'group' => false,
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.perdida_gestor')),
                                'group' => false
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.beneficio_participe')),
                                'group' => false
                            ]
                        ]
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.ingresos_arrendamientos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.ingresos_explotacion')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.ingresos_comisiones')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.ingresos_personal')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.ingresos_diversos')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '6' => [
                'name' => ucfirst(trans('fiscal.ingresos_financieros')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.ingresos_patrimonio')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.ingresos_deuda')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.ingresos_creditos')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.beneficios_razonable')),
                        'group' => false,
                        'level4' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.beneficios_negociacion')),
                                'group' => false
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.beneficios_empresa')),
                                'group' => false
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.beneficios_venta')),
                                'group' => false
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.beneficios_cobertura')),
                                'group' => false
                            ]
                        ]
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.beneficios_deuda')),
                        'group' => false,
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.ingresos_largo')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.diferencias_positivas_cambio')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.otros_ingresos_financieros')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '7' => [
                'name' => ucfirst(trans('fiscal.beneficios_excepcionales')),
                'group' => false,
                'level3' => [
                    '0' => [
                       'name' => ucfirst(trans('fiscal.beneficios_intangible')),
                       'group' => false,
                       'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.beneficios_material')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.beneficios_inmobiliarias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.beneficios_participaciones')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.diferencia_negocios')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.beneficios_propias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.ingresos_excepcionales')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ],
            '9' => [
                'name' => ucfirst(trans('fiscal.excesos_deterioro')),
                'group' => false,
                'level3' => [
                    '0' => [
                        'name' => ucfirst(trans('fiscal.reversion_intangible')),
                        'group' => false,
                        'level4' => false
                    ],
                    '1' => [
                        'name' => ucfirst(trans('fiscal.reversion_material')),
                        'group' => false,
                        'level4' => false
                    ],
                    '2' => [
                        'name' => ucfirst(trans('fiscal.reversion_inmobiliarias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '3' => [
                        'name' => ucfirst(trans('fiscal.reversion_existencias')),
                        'group' => false,
                        'level4' => false
                    ],
                    '4' => [
                        'name' => ucfirst(trans('fiscal.reversion_comerciales')),
                        'group' => false,
                        'level4' => false
                    ],
                    '5' => [
                        'name' => ucfirst(trans('fiscal.exceso_provisiones')),
                        'group' => false,
                        'level4' => false
                    ],
                    '6' => [
                        'name' => ucfirst(trans('fiscal.reversion_deuda_largo')),
                        'group' => false,
                        'level4' => false
                    ],
                    '7' => [
                        'name' => ucfirst(trans('fiscal.reversion_largo')),
                        'group' => false,
                        'level4' => false
                    ],
                    '8' => [
                        'name' => ucfirst(trans('fiscal.reversion_corto')),
                        'group' => false,
                        'level4' => false
                    ],
                    '9' => [
                        'name' => ucfirst(trans('fiscal.reversion_deterioro')),
                        'group' => false,
                        'level4' => false
                    ]
                ]
            ]
        ]
    ],
    8 => [
        'name' => ucfirst(trans('fiscal.gastos_patrimonio_neto')),
        'group' => '',
        'level2' =>  [

        ]
    ],
    9 => [
        'name' => ucfirst(trans('fiscal.ingresos_patrimonio_neto')),
        'group' => '',
        'level2' =>  [

        ]
    ]
];
@endphp

@foreach($data as $i1 => $r1)
    <div class="mb-5">
        <h3>{{ $i1 }} - {{ $r1['name'] }}</h3>

        @if(isset($r1['level2']))
            @foreach($r1['level2'] as $i2 => $r2)
                <div class="pl-3 mb-3">
                    {{ $i1 }}{{ $i2 }} - {{ $r2['name'] }}

                    @if(isset($r2['level3']))
                        @foreach($r2['level3'] as $i3 => $r3)
                            <div class="pl-3">
                                {{ $i1 }}{{ $i2 }}{{ $i3 }} - {{ $r3['name'] }}

                                @if($r3['level4'])
                                    @foreach($r3['level4'] as $i4 => $r4)
                                        <div class="pl-3">
                                            {{ $i1 }}{{ $i2 }}{{ $i3 }}{{ $i4 }} - {{ $r4['name'] }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        @endif
    </div>
@endforeach

@endsection
