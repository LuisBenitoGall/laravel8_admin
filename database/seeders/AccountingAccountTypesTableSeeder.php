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
        $data = $data = [
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
                    ]
                    '1' => [
                        'name' => ucfirst(trans('fiscal.reservas')),
                        'level3' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.prima_emision_asuncion'))
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
                                'name' => ucfirst(trans('fiscal.reserva_legal'))
                                'level4' => false
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.reservas_voluntarias'))
                                'level4' => false
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.reservas_especiales'))
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
                                'name' => ucfirst(trans('fiscal.reserva_perdidas_ganancias_actuariales'))
                                'level4' => false
                            ],
                            '8' => [
                                'name' => ucfirst(trans('fiscal.aportaciones_socios_propietarios'))
                                'level4' => false
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.diferencias_ajuste_capital_euros'))
                                'level4' => false
                            ]
                        ]
                    ]
                    '2' => [
                        'name' => ucfirst(trans('fiscal.resultado_pendientes_aplicacion')),
                        'level3' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.remanente'))
                                'level4' => false
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.resultados_negativos_ejercicios_anteriores'))
                                'level4' => false
                            ],
                            '9' => [
                                'name' => ucfirst(trans('fiscal.resultados_ejercicio'))
                                'level4' => false
                            ]
                        ]
                    ]
                    '3' => [
                        'name' => ucfirst(trans('fiscal.subvenciones_donaciones_ajustes_cambio_valor')),
                        'level3' => [
                            '0' => [
                                'name' => ucfirst(trans('fiscal.subvenciones_oficiales_capital'))
                                'level4' => false
                            ],
                            '1' => [
                                'name' => ucfirst(trans('fiscal.donaciones_legados_capital'))
                                'level4' => false
                            ],
                            '2' => [
                                'name' => ucfirst(trans('fiscal.otras_subvenciones_donaciones_legados'))
                                'level4' => false
                            ],
                            '3' => [
                                'name' => ucfirst(trans('fiscal.ajustes_valoracion_instrumentos_financieros'))
                                'level4' => false
                            ],
                            '4' => [
                                'name' => ucfirst(trans('fiscal.operaciones_cobertura'))
                                'level4' => [
                                    '0' => ['name' => ucfirst(trans('fiscal.cobertura_flujos_efectivo'))],
                                    '1' => ['name' => ucfirst(trans('fiscal.cobertura_inversion_neta_negocio_extranjero'))]
                                ]
                            ],
                            '5' => [
                                'name' => ucfirst(trans('fiscal.diferencias_conversion'))
                                'level4' => false
                            ],
                            '6' => [
                                'name' => ucfirst(trans('fiscal.ajustes_valoracion_activos_no_corrientes'))
                                'level4' => false
                            ],
                            '' => [
                                'name' => ucfirst(trans('fiscal.ingresos_fiscales_varios_ejercicios'))
                                'level4' => [
                                    '0' => ['name' => ucfirst(trans('fiscal.ingresos_fiscales_diferencias_permanentes'))],
                                    '1' => ['name' => ucfirst(trans('fiscal.ingresos_fiscales_deducciones_bonificaciones'))]    
                                ]
                            ]
                        ]
                    ]
                    '4' => [
                        'name' => ucfirst(trans('fiscal.provisiones')),
                        'level3' => [

                        ]
                    ]
                    '5' => [
                        'name' => ucfirst(trans('fiscal.deudas_largo_plazo')),
                        'level3' => [

                        ]
                    ]
                    '6' => [
                        'name' => ucfirst(trans('fiscal.deudas_largo_plazo_vinculadas')),
                        'level3' => [

                        ]
                    ]
                    '7' => [
                        'name' => ucfirst(trans('fiscal.deudas_largo_plazo_recibidos')),
                        'level3' => [

                        ]
                    ]
                    '8' => [
                        'name' => ucfirst(trans('fiscal.fianzas_depositos_largo_plazo')),
                        'level3' => [

                        ]
                    ]
                    '9' => [
                        'name' => ucfirst(trans('fiscal.situaciones_transitorias_financiacion'))
                        'level3' => [

                        ]
                    ]
                ]
            ]
            2 => [
                'name' => ucfirst(trans('fiscal.inmovilizado')),
                'level2' =>  [

                ]
            ]
            3 => [
                'name' => ucfirst(trans('fiscal.existencias')),
                'level2' =>  [

                ]
            ]
            4 => [
                'name' => ucfirst(trans('fiscal.acreedores_deudores')),
                'level2' =>  [

                ]
            ]
            5 => [
                'name' => ucfirst(trans('fiscal.cuentas_financieras')),
                'level2' =>  [

                ]
            ]
            6 => [
                'name' => ucfirst(trans('fiscal.compras_gastos')),
                'level2' =>  [

                ]
            ]
            7 => [
                'name' => ucfirst(trans('fiscal.ventas_ingresos')),
                'level2' =>  [

                ]
            ]
            8 => [
                'name' => ucfirst(trans('fiscal.gastos_patrimonio_neto')),
                'level2' =>  [

                ]
            ]
            9 => [
                'name' => ucfirst(trans('fiscal.ingresos_patrimonio_neto'))
                'level2' =>  [

                ]
            ]
        ];



        // $types = helpers::accountingAccountsTypes();
        // $subtypes = helpers::accountingAccountsSubtypes();

        // //Tipos:
        // foreach($types as $k => $type){
        //     $aat = new AccountingAccountType();
        //     $aat->code = $k;
        //     $aat->name = $type;
        //     $aat->save();          
        // }

        // //Subtipos:
        // foreach($subtypes as $k_ => $subtype){
        //     foreach($subtype as $j_ => $suboption){
        //         $aat = new AccountingAccountType();
        //         $aat->autoreference = $k_;
        //         $aat->code = $j_;
        //         $aat->name = $suboption;
        //         $aat->save();      
        //     }    
        // }
    }
}
