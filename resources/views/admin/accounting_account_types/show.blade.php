@extends('admin.partials.layout')

@section('content')


@php
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

        ]
    ],
    3 => [
        'name' => ucfirst(trans('fiscal.existencias')),
        'level2' =>  [

        ]
    ],
    4 => [
        'name' => ucfirst(trans('fiscal.acreedores_deudores')),
        'level2' =>  [

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
