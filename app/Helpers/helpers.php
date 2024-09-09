<?php
namespace App\Helpers;

use DateTime;

class Helpers{
	
	/**
	 * INDICE DE FUNCIONES:
	 * 1. Generales:
	 * 	1.1. Desglose de moneda.
	 * 	1.2. Niveles de acceso a los módulos.	
	 * 	1.3. Popover info.
	 *  1.4. Avatar.
	 *  1.5. Desglose de cuenta bancaria.
	 *
	 * 2. Fechas y hora:
	 * 	2.1. Convertir fecha a formato inglés.
	 * 	2.2. Meses multidioma.
	 * 	2.3. Días de la semana multidioma.
	 *  2.3.1. Días de la semana multidioma - semana inglesa.
	 *  2.4. Obtener día de la semana de una fecha.
	 *  2.5. Fecha formato literal.
	 *  2.6. Convertir a minutos.
	 *  2.7. Convertir a horas y minutos.
	 *  2.7.1. Convertir a segundos.
	 *  2.7.2. Convertir segundos a horas y minutos.
	 *  2.8. Intersección de fechas.
	 *  2.9. Obtener último día de un mes por año.
	 *  2.10. Calcular tiempo transcurrido entre horas.
	 *  2.11. Sumar horas.
	 *  2.12. Calcular fecha de vencimiento de factura.
	 *
	 * 3. Datos tipificados:
	 * 	3.1. Idiomas.
	 *  3.2. Datos públicos de empresa.
	 *  3.3. Tipo de datos.
	 *  3.4. Tipo de cuestiones para concursos.
	 *  3.5. Tipo de valores para las respuestas.
	 *  3.6. Modalidades de presupuesto.
	 *  3.7. Estados de presupuesto.
	 *  3.8. Estados línea de presupuesto con importe 0.
	 *  3.9. Estados en sala de chat.
	 *  3.10. Estados de pedido.
	 *  3.11. Estados de línea de pedido.
	 *  3.12. Estados de agrupaciones de pedidos.
	 *  3.13. Estados de proyectos.
	 *  3.14. Estados de entregas.
	 *  3.15. Tipos de vehículo.
	 *  3.16. Marcas de vehículo.
	 *  3.17. Estados de orden de trabajo.
	 *  3.18. Estados de productos de orden de trabajo.
	 *  3.18.1. Estados de producción para productos.
	 *  3.18.2. Estados de productos de orden de trabajo.
	 *  3.19. Modalidades de factura.
	 *  3.20. Estados de factura.
	 *  3.21. Métodos de pago.
	 *  3.22. Tipos de almacén.
	 *  3.23. Signo movimientos de stock.
	 *  3.24. Tipos de gastos.
	 *  3.25. Tipos de gráficos para proyectos.
	 *  3.26. Estados de SAT.
	 *  3.27. Estados de envíos.
	 *  3.28. Estados de protocolos.
	 *  3.29. Estados de venta por caja.
	 *  3.30. Modos de cuentas contables.
	 *  3.30.1. Tipos de cuentas contables.
	 *  3.30.2. Prefijos de cuentas contables específicas.
	 *  3.31. Diarios contables.
	 *  3.32. Tipos líneas de asientos contables.
	 *  3.33. Conceptos asientos contables.
	 *  3.34. Métodos contables.
	 *  3.35. Estados de efectos contables.
	 *  3.36. Tipos de efectos contables.
	 *  3.37. Estados de pagos.
	 *  3.38. Estados de remesas contables.
	 *
	 * 4. Strings & Arrays:
	 * 	4.1. Truncar texto.
	 * 	4.2. Slug string para archivos.
	 *  4.3. Customizar CIF.
	 *  4.3.1. Setear NIF.
	 *  4.4. Formato numérico para decimales.
	 *  4.5. Customizar strings.
	 *  4.6. Cadena serializada.
	 *  4.7. Detectar valores repetidos en un array.
	 *
	 * 5. Archivos:
	 *  5.1. Allowed Image Mime Types.
	 *  5.2. Extensiones según Mime Types.
	 *  5.3. Iconos vinculados a archivos.
	 *
	 * 6. Cálculos:
	 *  6.1. Porcentaje.
	 *  6.2. Importe de línea de presupuesto-pedido.
	 *
	 * 7. HTML:
	 * 	7.1. Render galería imágenes.
	 * 	7.2. Render documentos proyectos.
	 * 	7.3. Render documentos productos.
	 * 	7.4. Scroll to top.
	 */ 
	
	/**
	 * 1.1. Desglose de moneda.
	 */
	public static function splitCurrency($currency, $part = false){
		$parts = explode(';', $currency);

		if($part){
			switch($part){
				case 'code':
					return $parts[0];
					break;
				case 'number':
					return $parts[1];
					break;
				case 'symbol':
					return $parts[2];
					break;
			}
		}else{
			return $parts;
		}
	}

	/**
	 * 1.2. Niveles de acceso a los módulos.
	 */
	public static function levels($level = false){
		$data = [
			1 => 'Módulos de Administrador',
			2 => 'Módulos básicos',
			3 => 'Módulos optativos'
		];

		if($level){
			return $data[$level];
		}else{
			return $data;
		}
	}

	/**
	 * 1.3. Popover info.
	 */
	public static function info($code = false, $for_chk = false){
		if($code){
			$data = json_decode(file_get_contents(storage_path()."/json/contents.json"), true);
			$content = false;

			if($data){
				foreach($data as $key => $row){
					if($row['code'] == $code){
						$content = $row;
					}
				}

				if($content){
					$idm = (session()->get('locale') != NULL)? session()->get('locale'):App::getLocale();

					$titulo = unserialize($content['title']);
					$destacado = unserialize($content['excerpt']);

					$contenido = $destacado[$idm];
					if(isset($content['referrer'])){
						$contenido .= '<br><a target="_blank" href="'.$content['referrer'].'">'.trans('textos.leer_mas').'</a>';
					}

					//Adaptación .css para checkbox:
					$chk = $for_chk? 'for_chk':'';

					$html = '<a role="button" href="#" class="pop-over '.$chk.'" data-toggle="popover" data-trigger="focus" data-placement="top" title="'.$titulo[$idm].'" data-content="'.$contenido.'" data-original-title="'.$titulo[$idm].'"><i class="fa fa-info"></i></a>';

					return $html;
				}
			}
		}
	}

	/**
	 * 1.4. Avatar.
	 */
	public static function setAvatar($str1, $str2 = false){
		$arr = [
			'A' => '11E',
	        'B' => '22D',
	        'C' => '33C',
	        'D' => '44B',
	        'E' => '55A',
	        'F' => '669',
	        'G' => 'EE9',
	        'H' => 'DD8',
	        'I' => 'CC7',
	        'J' => 'BB6',
	        'K' => 'AA5',
	        'L' => '994',
	        'M' => 'FF1',
	        'N' => '1FF',
	        'O' => '449',
	        'P' => '55A',
	        'Q' => '66B',
	        'R' => '77C',
	        'S' => '88D',
	        'T' => '99E',
	        'U' => '966',
	        'V' => 'AA5',
	        'W' => 'BB4',
	        'X' => 'CC3',
	        'Y' => 'DD2',
	        'Z' => 'EE1'
	    ];

		// Convertimos a ASCII, para soportar cualquier caracter
		$strA = mb_convert_encoding($str1, "ASCII");
		$strB = mb_convert_encoding($str2, "ASCII");

		// Obtenemos el primer caracter
        $l1 = strtoupper(substr($strA, 0, 1));
        $l2 = $strB? strtoupper(substr($strB, 0, 1)):'';

		// Determinamos el color del icono
		$bg1 = array_key_exists($l1,$arr) ? $arr[$l1] : '727' ;
		$bg2 = array_key_exists($l2,$arr) ? $arr[$l2] : '96f' ;
        $bg = "#$bg1$bg2";

		// Generamos el html tag
        return "<span class='avatar' style='background-color: $bg'>$l1 $l2</span>";
	}

	/**
	 * 1.5. Desglose de cuenta bancaria.
	 */
	public static function splitBankAccount($account){
		$data = [];
		$account = trim($account);
        $account = str_replace(array(" ", "-"), "", $account);

		if(strlen($account) == 24){
            $data['iban'] = substr($account, 0, 4);
            $data['entity'] = substr($account, 4, 4);
            $data['office'] = substr($account, 8, 4);
            $data['dc'] = substr($account, 12, 2);
            $data['digits'] = substr($account, 14, 10); 
            $data['account'] = strtoupper($data['iban']).' '.$data['entity'].' '.$data['office'].' '.$data['dc'].' '.$data['digits'];
        }

        return $data; 	
	}

	/**
	 * 2.1. Convertir fecha a formato inglés.
	 */
	public static function convertFecha($fecha, $hora = false){
		if($hora){
			$partes = explode(' ', $fecha);
			$p = explode('/', $partes[0]);
		}else{
			$p = explode('/', $fecha);	
		}

		if($p){
			$newfecha = $p[2].'-'.$p[1].'-'.$p[0];
			if($hora){
				$newfecha .= ' '.$partes[1];
			}
			return $newfecha;	
		}
	}

	/**
	 * 2.2. Meses multidioma.
	 *
	 * @param  int $valor  
 	 * @param  str $idioma 
 	 * @return str
	 */
	public static function yearMonths($mes = false){
		$data = [
			'01' => trans('textos.enero'), 
			'02' => trans('textos.febrero'), 
			'03' => trans('textos.marzo'), 
			'04' => trans('textos.abril'),
			'05' => trans('textos.mayo'), 
			'06' => trans('textos.junio'),
			'07' => trans('textos.julio'),
			'08' => trans('textos.agosto'),
			'09' => trans('textos.septiembre'),
			'10' => trans('textos.octubre'),
			'11' => trans('textos.noviembre'),
			'12' => trans('textos.diciembre')
		];
		if($mes){
			return $data[$mes];
		}else{
			return $data;
		}		
	}

	/**
	 * 2.3. Días de la semana multidioma.
	 *
	 * @param  int $valor  
 	 * @param  str $idioma 
 	 * @return str 
	 */
	public static function weekDays($dia = false){
		$data = [
			trans('textos.lunes'), 
			trans('textos.martes'), 
			trans('textos.miercoles'), 
			trans('textos.jueves'),
			trans('textos.viernes'), 
			trans('textos.sabado'),
			trans('textos.domingo')
		];
		if($dia){
			return $data[$dia];
		}else{
			return $data;
		}	
	}

	/**
	 * 2.3.1. Días de la semana multidioma - semana inglesa.
	 */ 
	public static function weekDaysEn($dia = false){
		$data = [
			1 => trans('textos.lunes'), 
			2 => trans('textos.martes'), 
			3 => trans('textos.miercoles'), 
			4 => trans('textos.jueves'),
			5 => trans('textos.viernes'), 
			6 => trans('textos.sabado'),
			7 => trans('textos.domingo')
		];
		if($dia){
			return $data[$dia];
		}else{
			return $data;
		}	
	}

	/**
	 * 2.4. Obtener día de la semana de una fecha.
	 */
	public static function getWeekDay($dia, $mes, $any){
		$numerodiasemana = date('w', mktime(0,0,0,$mes,$dia,$any));
	    if($numerodiasemana == 0)
	       $numerodiasemana = 6;
	    else
	       $numerodiasemana--;
	    return $numerodiasemana;
	}

	/**
	 * 2.5. Fecha formato literal.
	 *
	 * @param  datetime $fecha  
 	 * @param  str $idioma
	 */
	public static function fechaLiteral($fecha, $idioma){
		$p = explode(' ', $fecha);
		$q = explode('-', $p[0]);
		$weekDay = date('w', strtotime($p[0]));

		$arrDias = [
			0 => 7,
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
			5 => 5,
			6 => 6
		];

		$dia = Helpers::weekDaysEn($arrDias[$weekDay]);
		$mes = Helpers::yearMonths($q[1]);

		switch($idioma){
			case 'es':
				$resp = $dia.', '.$q[2].' de '.$mes.' de '.$q[0];
				break;
			case 'ca':
				$resp = $dia.', '.$q[2].' de '.$mes.' de '.$q[0];
				break;
			case 'en':
				$resp = $dia.', '.$q[2].' de '.$mes.' de '.$q[0];
				break;
			case 'po':
				$resp = $dia.', '.$q[2].' de '.$mes.' de '.$q[0];
				break;
		}	
		
		return $resp;
	}

	/**
	 * 2.6. Convertir a minutos.
	 */
	public static function getMinutes($hora){
		if($hora != ''){
			$p = explode(':',$hora);
			$hminutos = intval($p[0]) * 60;
			$m = isset($p[1])? $p[1]:'0';
;			$minutos = $hminutos + $m;
			return $minutos;
		}else{
			return false;
		}
	}

	/**
	 * 2.7. Convertir a horas y minutos.
	 */
	public static function getTimeFromMinutes($minutos){
		$h = floor($minutos / 60);
		if($h < 10) $h = '0'.$h;
		$m = $minutos%60;
		if($m < 10) $m = '0'.$m;
		$horas = $h.':'.$m;
		return $horas;
	}

	/**
	 * 2.7.1. Convertir a segundos.
	 */
	public static function getSeconds($hora){
		if($hora != ''){
			$minutes = Helpers::getMinutes($hora);
			$seconds = $minutes * 60;
			return $seconds;
		}else{
			return false;
		}
	}

	/**
	 * 2.7.2. Convertir segundos a horas y minutos.
	 */
	public static function secondsToHoursMinutes($seconds){
		if($seconds > 0){
			$hours = floor($seconds/ 3600);
            $minutes = floor(($seconds - ($hours * 3600)) / 60);
            $seconds = $seconds - ($hours * 3600) - ($minutes * 60);

            $hora = $hours.':'.$minutes.":".$seconds;

            if($minutes < 10) $minutes = '0'.$minutes;

            if($seconds == 0) $hora = $hours.':'.$minutes;
         
            return $hora;
		}else{
			return false;
		}
	}

	/**
	 * 2.8. Intersección de fechas.
	 */
	public static function shareDateDays($startDate1, $endDate1, $startDate2, $endDate2){
    	$days = 0;
        $startDate1 = strtotime($startDate1); // fecha de inicio 1
        $endDate1 = strtotime($endDate1); // fecha de finalización 1
        $startDate2 = strtotime($startDate2); // fecha de inicio 2
        $endDate2 = strtotime($endDate2); // fecha de finalización 2
 
        // ** ------------ Conversión de valor crítico ------ inicio ------ * /
        // Si la fecha de finalización de la fecha 1 es menor que la fecha de inicio de la fecha 2, devuelve 0
    	if($endDate1 < $startDate2){
        	$days = 0;
    	}
        // Si la fecha de inicio de la fecha 1 es menor que la fecha de finalización de la fecha 2, devuelve 0
    	if($startDate1 > $endDate2){
        	$days = 0;
    	}
        // Si la fecha de finalización de la fecha 1 es igual a la fecha de inicio de la fecha 2, devuelve 1
    	if($endDate1 == $startDate2){
        	$days = 0;
    	}
        // Si la fecha de inicio de la fecha 1 es igual a la fecha de finalización de la fecha 2, devuelve 1
    	if($startDate2 == $endDate1){
        	$days = 0;
    	}
        // ** ------------ Conversión de valor crítico ------ fin ------ * /
 
        // ** ------------ Conversión de intersección ------ inicio ------ * /
        // Si la fecha de inicio 1 es menor que la fecha de inicio 2 y la fecha de inicio 2 es menor que la fecha de finalización 1
    	if($startDate1 < $startDate2 && $endDate1 > $startDate2){
            // Si la fecha de finalización 1 es menor o igual que la fecha de finalización 2
        	if($endDate1 < $endDate2){
            	$days = Helpers::diffBetweenTwoDays($startDate2, $endDate1);
        	}
            // Si la fecha de finalización 1 es mayor que la fecha de finalización 2
        	if($endDate1 > $endDate2){
            	$days = Helpers::diffBetweenTwoDays($startDate2, $endDate2);
        	}
    	}
 
        // Si la fecha de inicio 1 es mayor que la fecha de inicio 2 y la fecha de inicio 1 es menor que la fecha de finalización 2
    	if($startDate1 > $startDate2 && $startDate1 < $endDate2){
            // Si la fecha de finalización 1 es menor o igual que la fecha de finalización 2
        	if($endDate1 < $endDate2){
            	$days = Helpers::diffBetweenTwoDays($startDate1, $endDate2);
        	}
            // Si la fecha de finalización 1 es mayor que la fecha de finalización 2
        	if($endDate1 > $endDate2){
            	$days = Helpers::diffBetweenTwoDays($startDate1, $endDate2);
        	}
    	}
        // la fecha de inicio 1 es igual a la fecha de inicio 2
    	if($startDate1 == $startDate2){
            // la fecha de finalización 1 es menor o igual que la fecha de finalización 2
	        if($endDate1 < $endDate2){
	            $days = Helpers::diffBetweenTwoDays($startDate1, $endDate1);
	        }
	        // la fecha de finalización 1 es mayor que la fecha de finalización 2
	        if($endDate1 > $endDate2){
	            $days = Helpers::diffBetweenTwoDays($startDate1, $endDate2);
	        }
    	}
        // la fecha de finalización 1 es igual a la fecha de finalización 2
    	if($endDate1 == $endDate2){
            // la fecha de inicio 1 es menor o igual que la fecha de inicio 2
        	if($startDate1 < $startDate2){
            	$days = Helpers::diffBetweenTwoDays($startDate2, $endDate1);
        	}
            // la fecha de inicio 1 es mayor que la fecha de inicio 2
        	if($startDate1 > $startDate2){
            	$days = Helpers::diffBetweenTwoDays($startDate1, $endDate1);
        	}
    	}
        // El período de tiempo 1 está dentro del período de tiempo 2
    	if($startDate1 > $startDate2 && $endDate1 < $endDate2){
        	$days = Helpers::diffBetweenTwoDays($startDate1, $endDate1);
    	}
        // El período de tiempo 1 incluye el período de tiempo 2
    	if($startDate1 < $startDate2 && $endDate1 > $endDate2){
        	$days = Helpers::diffBetweenTwoDays($startDate2, $endDate2);
    	}
        // ** ------------ Conversión de intersección ------ fin ------ * /
 
    	return $days;
	}

	/**
	 * 2.9. Obtener último día de un mes por año.
	 */
	public static function getMonthLastDay($mes, $any){
		$ultimo_dia=28;
	    while (checkdate($mes,$ultimo_dia + 1,$any)){
	       $ultimo_dia++;
	    }
	    return $ultimo_dia; 
	}

	/**
	 * 2.10. Calcular tiempo transcurrido entre horas.
	 */
	public static function getTimeBetweenHours($from, $to){
		if($from == 0) $from = '00:00';
		if($to == 0) $to = '00:00';

		if($from > 24){
			$p = explode(':', $from);
			$dias = intdiv($p[0], 24);
			$horas = $p[0] - (24*$dias);
			//$minutos = $p[1] < 10? '0'.$p[1]:$p[1];
			$minutos = $p[1];
			//if($minutos < 10) $minutos = '0'.$minutos;
			$from = $dias.':'.$horas.':'.$minutos;
		}

		if($to > 24){
			$p = explode(':', $to);
			$dias = intdiv($p[0], 24);
			$horas = $p[0] - (24*$dias);
			//$minutos = $p[1] < 10? '0'.$p[1]:$p[1];
			$minutos = $p[1];
			//if($minutos < 10) $minutos = '0'.$minutos;
			$to = $dias.':'.$horas.':'.$minutos;
		}

		if(strlen($from) > 2 && substr($from, 0, 2) == '00') $from = substr($from, 1);
		if(strlen($to) > 2 && substr($to, 0, 2) == '00') $to = substr($to, 1);
		
		$f = new DateTime($from);
		$i = new DateTime($to);

		$tiempo = $f->diff($i);

		return $tiempo->format('%H:%I'); 
	}

	/**
	 * 2.11. Sumar horas.
	 */
	public static function sumaHoras($a, $b){
		$pa = ($a != '00:00:00')? explode(':', $a):['00','00'];
		$pb = ($b != '00:00:00')? explode(':', $b):['00','00']; 

		$horas = intval($pa[0]) + intval($pb[0]);
		$minutos = intval($pa[1]) + intval($pb[1]);

		if($minutos >= 60){
			$minutos = $minutos - 60;
			$horas++;
		}

		if($horas < 10) $horas = '0'.$horas;
		if(strlen($minutos) == 1) $minutos = '0'.$minutos; 

		return $horas.':'.$minutos;
	}

	/**
	 * 2.12. Calcular fecha de vencimiento de factura.
	 */
	public static function calculateExpirationDate($date, $after_days, $payday){
		$f = date("Y-m-d", strtotime($date."+ ".$after_days." days"));
		$p = explode('-', $f);

		//Ajustando fecha de pago:
		$month = $p[1];	
		$year = $p[0];
		//$day = (int)$payday > (int)$p[2]? $payday:$p[2];
		$day = (int)$payday > 0? $payday:$p[2];
		if($payday > 0 && (int)$p[2] > $payday){
			$month = intval($month) + 1;
			if($month == 13){
				$month = 1;
				$year = $year++;	
			} 
			$month = $month < 10? '0'.$month:$month;
		}

		return $year.'-'.$month.'-'.$day;
	}
	
	/**
	 * 3.1. Idiomas.
	 *
	 * Agrega el código de lenguaje al siguiente array.
     * El código debe tener el mismo nombre que en la carpeta de lenguajes.
     * Asegúrate de que estén en orden alfabético.
     * El selector de lenguaje no se verá en front si solo hay un lenguaje en el array.
	 */
	public static function languages($lang = false){
		$data = [
	        /*
	         * Key is the Laravel locale code
	         * Index 0 del sub-array es el Carbon locale code
	         * Index 1 del sub-array es el PHP locale code para setlocale()
	         * Index 2 del sub-array es true o false si usa RTL (right-to-left) css para el lenguaje
	         * Index 3 del sub-array es el literal del idioma
	         * Index 4 del sub-array para el formato de fecha
	         * Index 5 del sub-array para estado (activo - inactivo) del idioma
	         */

	        'es'    => ['es', 'es_ES', false, 'Castellano', 'd/m/Y', true],
	        'ca'    => ['ca', 'ca_CA', false, 'Català', 'd/m/Y', true],
	        'en'    => ['en', 'en_US', false, 'English', 'Y/m/d', true]
	    ];

	    if($lang){
	    	return $data[$lang];
	    }else{
	    	return $data;
	    }
	}

	/**
	 * 3.2. Datos públicos de empresa.
	 *
	 * Campos de información sobre la empresa que el usuario decide hacer públicos o privados.
	 */
	public static function publicInfo(){
		$data = [
			'ba' => trans('textos.areas_negocio'),
			'wp' => trans('textos.centros_trabajo'),
			'cc' => trans('textos.centros_coste')
		];

		return $data;
	}

	/**
	 * 3.3. Tipo de datos.
	 */
	public static function dataTypes($type = false){
		/**
		 * Leyenda array:
		 *
		 * 0 = nombre del tipo.
		 * 1 = indica si requiere valores.
		 */		
		$data = [
			10 => [ucfirst(trans('textos.booleano')), [trans('textos.si'), trans('textos.no')], 'boolean'],
			20 => [ucfirst(trans('textos.texto')), false, 'textarea'],
			30 => [ucfirst(trans('textos.numerico')), false, 'textarea'],
			40 => [ucfirst(trans('textos.fecha')), false, 'datepicker'],
			50 => [ucfirst(trans('textos.opciones')), true, 'select'],
			60 => [ucfirst(trans('textos.color')), true, 'color']	
		];

		if($type){
			return $data[$type];
		}else{
			return $data;
		}		
	}	

	/**
	 * 3.4. Tipo de cuestiones para concursos.
	 */
	public static function questionTypes($type = false){
		$data = [
			10 => ucfirst(trans('textos.pregunta_test')),
			20 => ucfirst(trans('textos.texto_libre')),
			30 => ucfirst(trans('textos.crucigrama'))
		];

		if($type){
			return $data[$type];
		}else{
			return $data;
		}			
	}

	/**
	 * 3.5. Tipo de valores para las respuestas.
	 */
	public static function answerValueTypes($type = false){
		$data = [
			10 => ucfirst(trans('textos.verdadero_falso')),
			20 => ucfirst(trans('textos.puntuacion'))
		];

		if($type){
			return $data[$type];
		}else{
			return $data;
		}			
	}

	/**
	 * 3.6. Modalidades de presupuesto.
	 */
	public static function budgetModalities($modality = false){
		$data = [
			1 => trans('textos.presupuesto_basico'),
			10 => trans('textos.presupuesto_por_formulario'),
			20 => trans('textos.presupuesto_por_catalogo')
		];
		if($modality){
			return $data[$modality];
		}else{
			return $data;
		}	
	}

	/**
	 * 3.7. Estados de presupuesto.
	 */
	public static function budgetStatus($status = false){
		$data = [
			1 => ucfirst(trans('textos.borrador')),
			10 => ucfirst(trans('textos.enviado')),
			20 => ucfirst(trans('textos.aprobado')),
			30 => ucfirst(trans('textos.cancelado')),
			40 => ucfirst(trans('textos.cerrado'))
		];
		if($status){
			return $data[$status];
		}else{
			return $data;
		}	
	}

	/**
	 * 3.8. Estados línea de presupuesto con importe 0.
	 */
	public static function statusBudgetLinesWithoutPrice($status = false){
		$data = [
			'0',
			trans('textos.pendiente'),
			trans('textos.sin_coste')
		];
		if($status){
			return $data[$status];
		}else{
			return $data;
		}			
	}

	/**
	 * 3.9. Estados en sala de chat.
	 */
	public static function chatStatus($status = false){
		$data = [
			1 => ucfirst(trans('textos.usuario')),
			10 => ucfirst(trans('textos.administrador'))
		];
		if($status){
			if(isset($data[$status])){
				return $data[$status];
			}else{
				return false;
			}
		}else{
			return $data;
		}	
	}

	/**
	 * 3.10. Estados de pedido.
	 *
	 * Las constantes ORDER_STATUS_INVOICED_ y ORDER_STATUS_NOT_INVOICED_ (config/constants.php) manejan los estados de pedido facturables. Incluir en sus respectivos arrays los valores según un pedido en determinado estado se puede o no facturar.
	 */
	public static function orderStatus($status = false){
		$data = [
			//0 => '',
			//1 => trans('textos.pendiente'),
			10 => trans('textos.aprobado'),
			//15 => trans('textos.cliente_dudas'),
			//20 => trans('textos.trabajando'),
			//25 => trans('textos.porcentaje'),
			30 => trans('textos.cancelado'),
			//40 => trans('textos.entregado'),
			//50 => trans('textos.incidencia'),
			//60 => trans('textos.rondas_cambio'),
			70 => trans('textos.finalizado'),
			//80 => trans('textos.pendiente_feedback'),
			//90 => trans('textos.preparacion_aaff')
		];
		if($status){
			if(isset($data[$status])){
				return ucfirst($data[$status]);
			}else{
				return false;
			}
		}else{
			return $data;
		}		
	}

	/**
	 * 3.11. Estados de línea de pedido.
	 */
	public static function orderLineStatus($status = false){
		$data = [
			0 => '',
			//1 => trans('textos.pendiente'),
			10 => trans('textos.aprobado'),
			15 => trans('textos.cliente_resolucion_dudas'),
			20 => trans('textos.trabajando'),
			30 => trans('textos.pendiente_feedback').' 1',
			40 => trans('textos.ronda_cambios').' 1',
			31 => trans('textos.pendiente_feedback').' 2',
			41 => trans('textos.ronda_cambios').' 2',
			32 => trans('textos.pendiente_feedback').' 3',
			42 => trans('textos.ronda_cambios').' 3',
			33 => trans('textos.pendiente_feedback').' 4',
			43 => trans('textos.ronda_cambios').' 4',
			34 => trans('textos.pendiente_feedback').' 5',
			44 => trans('textos.ronda_cambios').' 5',
			35 => trans('textos.pendiente_feedback').' 6',
			45 => trans('textos.ronda_cambios').' 6',
			50 => trans('textos.preparacion_aaff'),
			60 => trans('textos.cancelado'),
			70 => trans('textos.finalizado')
		];
		if($status){
			if(isset($data[$status])){
				return $data[$status];
			}else{
				return false;
			}
		}else{
			return $data;
		}			
	}

	/**
	 * 3.12. Estados de agrupaciones de pedidos.
	 */
	public static function orderGroupStatus($status = false){
		$data = [
			1 => trans('textos.cerrado'),
			10 => trans('textos.abierto'),
			20 => trans('textos.facturado')
		];
		if($status){
			if(isset($data[$status])){
				return ucfirst($data[$status]);
			}else{
				return false;
			}
		}else{
			return $data;
		}		
	}

	/**
	 * 3.13. Estados de proyectos.
	 */
	public static function projectStatus($status = false){
		$data = [
			1 => trans('textos.inicio'),
			10 => trans('textos.en_curso'),
			20 => trans('textos.pendiente_aprobacion'),
			30 => trans('textos.finalizado')
		];
		if($status){
			if(isset($data[$status])){
				return ucfirst($data[$status]);
			}else{
				return false;
			}
		}else{
			return $data;
		}		
	}

	/**
	 * 3.14. Estados de entregas.
	 */
	public static function deliveryStatus($status = false){
		$data = [
			1 => trans('textos.en_preparacion'),
			10 => trans('textos.pendiente_envio'),
			20 => trans('textos.enviado'),
			30 => trans('textos.entregado')
		];
		if($status){
			if(isset($data[$status])){
				return ucfirst($data[$status]);
			}else{
				return false;
			}
		}else{
			return $data;
		}		
	}

	/**
	 * 3.15. Tipos de vehículo.
	 */
	public static function vehicleTypes($type = false){
		$data = [
			1 => trans('textos.coche'),
			2 => trans('textos.moto'),
			3 => trans('textos.furgoneta'),
			4 => trans('textos.camion'),
			5 => trans('textos.trailer'),
			6 => trans('textos.autocar'),
			200 => trans('textos.otros')
		];
		if($type){
			return ucfirst($data[$type]);
		}else{
			return $data;
		}	
	}

	/**
	 * 3.16. Marcas de vehículo.
	 */
	public static function vehicleBrands($brand = false){
		$data = [
			1 => 'seat',
			2 => 'renault',
			3 => 'nissan',
			4 => 'peugeot',
			5 => 'citroen',
			6 => 'fiat',
			7 => 'audi',
			8 => 'mercedes'
		];
		if($brand){
			return ucfirst($data[$brand]);
		}else{
			return $data;
		}			
	}

	/**
	 * 3.17. Estados de orden de trabajo.
	 */
	public static function workOrderStatus($status = false){
		$data = [
			1 => trans('textos.en_planificacion'),
			10 => trans('textos.en_produccion'),
			20 => trans('textos.terminada')			
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}		
	}

	/**
	 * 3.18. Estados de productos de orden de trabajo.
	 */
	public static function workOrderProductStatus($status = false){
		$data = [
			1 => trans('textos.en_espera'),
			10 => trans('textos.en_produccion'),
			20 => trans('textos.en_revision'),
			30 => trans('textos.completado'),
			40 => trans('textos.en_stock')
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}		
	}

	/**
	 * 3.18.1. Estados de producción para productos.
	 */
	public static function productionProductStatus($status = false){
		$data = [
			1 => trans('textos.materia_prima'),
			10 => trans('textos.semielaborado'),
			40 => trans('textos.producto_acabado')
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}		
	}

	/**
	 * 3.18.2. Estados de componentes de orden de trabajo.
	 */
	public static function workOrderComponentStatus($status = false){
		$data = [
			1 => trans('textos.pendiente'),
			10 => trans('textos.en_preparacion'),
			20 => trans('textos.en_produccion'),
			30 => trans('textos.completado'),
			40 => trans('textos.incidencia')
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}		
	}

	/**
	 * 3.19. Modalidades de factura.
	 */
	public static function invoiceModalities(){
		// t = total;
		// p = parcial;
		$data = ['t', 'p'];
		return $data;
	}

	/**
	 * 3.20. Estados de factura.
	 */
	public static function invoiceStatus($status = false){
		$data = [
			1 => trans('textos.pendiente'),
			10 => trans('textos.abonada'),
			20 => trans('textos.devuelta')
		];
		if($status){
			return $data[$status];
		}else{
			return $data;
		}		
	}

	/**
	 * 3.21. Métodos de pago.
	 */
	public static function paymentMethods($method = false){
		$data = [
			1 => trans('textos.transferencia_bancaria'),
			10 => trans('textos.efectivo'),
			20 => trans('textos.talon_bancario'),
			30 => trans('textos.tarjeta'),
			40 => trans('textos.domiciliado')
		];
		if($method){
			return $data[$method];
		}else{
			return $data;
		}	
	}

	/**
	 * 3.22. Tipos de almacén.
	 */
	public static function storeTypes($type = false){
		$data = [
			'r' => trans('textos.almacen_regular'),
			'i' => trans('textos.almacen_incidencias'),
			'c' => trans('textos.almacen_consumo_interno')
		];
		if($type){
			return $data[$type];
		}else{
			return $data;
		}
	}	

	/**
	 * 3.23. Signo movimientos de stock.
	 */
	public static function signStockMovements($sign = false){
		$data = [
			's' => trans('textos.sumar'),
			'r' => trans('textos.restar'),
			'x' => trans('textos.traspaso'),
			'y' => trans('textos.devolucion_interna')
		];
		if($sign){
			return $data[$sign];
		}else{
			return $data;
		}
	}

	/**
	 * 3.24. Tipos de gastos.
	 */
	public static function expenseTypes($type = false){
		$data = [
			10 => trans('textos.pago_efectivo'),
			20 => trans('textos.pago_tarjeta'),
			30 => trans('textos.dietas')
		];
		if($type){
			return $data[$type];
		}else{
			return $data;
		}	
	}

	/**
	 * 3.25. Tipos de gráficos para proyectos.
	 * Permite seleccionar el gráfico principal que deseas visualizar en el proyecto.
	 */
	public static function projectCharts($chart = false){
		$data = [
			'be' => 'Balance económico',
			'pp' => 'Progreso del proyecto'
		];
		if($chart){
			return $data[$chart];
		}else{
			return $data;
		}	
	}

	/**
	 * 3.26. Estados de SAT.
	 */
	public static function satStatus($status = false){
		$data = [
			1 => trans('textos.pendiente'),
			10 => trans('textos.en_curso'),
			20 => trans('textos.terminada')			
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}		
	}

	/**
	 * 3.27. Estados de envíos.
	 */
	public static function shipmentStatus($status = false){
		$data = [
			1 => trans('textos.abierto'),
			10 => trans('textos.en_ruta'),
			20 => trans('textos.cerrado')			
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}		
	}

	/**
	 * 3.28. Estados de protocolos.
	 */
	public static function protocolStatus($status = false){
		$data = [
			1 => trans('textos.borrador'),
			10 => trans('textos.activo'),
			20 => trans('textos.inactivo')			
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}		
	}

	/**
	 * 3.29. Estados de venta por caja.
	 */
	public static function cashStatus($status = false){
		$data = [
			1 => ucfirst(trans('textos.en_curso')),
			10 => ucfirst(trans('textos.abonado')),
			20 => ucfirst(trans('textos.devuelto'))
		];
		if($status){
			return $data[$status];
		}else{
			return $data;
		}	
	}

	/**
	 * 3.30. Modos de cuentas contables.
	 */
	public static function accountingAccountModes($mode = false){
		$data = [
			1 => ucfirst(trans('textos.cliente')),
			2 => ucfirst(trans('textos.proveedor')),
			3 => ucfirst(trans('textos.banco')),
			4 => ucfirst(trans('textos.contabilidad'))
		];
		if($mode){
			return $data[$mode];
		}else{
			return $data;
		}		
	}

	/**
	 * 3.30.1. Tipos de cuentas contables.
	 */
	public static function accountingAccountTypes($type = false){
		$data = [
			'a' => ucfirst(trans('textos.activos')),
			'p' => ucfirst(trans('textos.pasivos')),
			'g' => ucfirst(trans('textos.perdidas_ganancias'))
		];
		if($type){
			return $data[$type];
		}else{
			return $data;
		}		
	}

	/**
	 * 3.30.2. Prefijos de cuentas contables específicas.
	 */
	public static function accountingAccountSpecificPrefixes(){
		return [
			'provider' => '4000',
        	'creditor' => '4100',
            'customer' => '4300'
		];
	}

	/**
	 * 3.31. Diarios contables.
	 */
	public static function accountingDiaries($diary = false){
		$data = [
			'dg' => trans('textos.diario_general'),
			//'PG' => 'Diario Pagos',
			'dc' => trans('textos.diario_compras'),
			'dv' => trans('textos.diario_ventas'),
			//'RB' => 'Remesas Bancarias',
			//'CB' => 'Diario Cobros',
			'dj' => trans('textos.diario_caja'),
			//'IMPAG' => 'Impagados'
		];
		if($diary){
			if(isset($data[$diary])){
				return $data[$diary];
			}else{
				return false;
			}
		}else{
			return $data;
		}
	}

	/**
	 * 3.32. Tipos líneas de asientos contables.
	 */
	public static function seatLinesTypes($type = false){
		$data = [
			'cp' => trans('textos.cheque_pagare'),
			'ir' => 'IR',
			'iv' => 'IV',
			'tr' => trans('textos.transferencia'),
			'vc' => trans('textos.venta_contado')
		];
		if($type){
			return ucfirst($data[$type]);
		}else{
			return $data;
		}
	}

	/**
	 * 3.33. Conceptos asientos contables.
	 */
	public static function seatConcepts($concept = false){
		$data = [
			'CF' => trans('textos.cobro_factura'),
			'IMPAG' => trans('textos.impagado'),
			'LI' => trans('textos.liquidacion'),
			'NF' => trans('textos.factura_nuestra'),
			//'NO' => trans('textos.no'),
			'NP' => trans('textos.pago_nuestro'),
			'NR' => trans('textos.remesa_num'),
			'PYS' => trans('textos.paga_senal'),
			'SF' => trans('textos.factura_proveedor')
		];
		if($concept){
			if(isset($data[$concept])){
				return ucfirst($data[$concept]);
			}else{
				return false;
			}
		}else{
			return $data;
		}
	}

	/**
	 * 3.34. Métodos contables.
	 */
	public static function accountingMethods($method = false){
		$data = [
			1 => ucfirst(trans('textos.devengo')),
			2 => ucfirst(trans('textos.caja')),
			3 => ucfirst(trans('textos.competencia')),
			4 => ucfirst(trans('textos.porcentaje_completado')),
			5 => ucfirst(trans('textos.unidad_produccion')),
			6 => ucfirst(trans('textos.costo_historico')),
			7 => ucfirst(trans('textos.revaluacion')),
			8 => ucfirst(trans('textos.ingresos'))
		];	

		if($method){
			if(isset($data[$method])){
				return ucfirst($data[$method]);
			}else{
				return false;
			}
		}else{
			return $data;
		}
	}

	/**
	 * 3.35. Estados de efectos contables.
	 */
	public static function effectsStatus($status = false){
		$data = [
			1 => trans('textos.aceptado'),
			10 => trans('textos.aceptado_no'),
			20 => trans('textos.cobrado')
			// 30 => trans('textos.impagado')
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}	
	}

	/**
	 * 3.36. Tipos de efectos contables.
	 */
	public static function effectTypes($type = false){
		$data = [
			10 => ucfirst(trans('textos.efectivo')),
			20 => ucfirst(trans('textos.cheque')),
			30 => ucfirst(trans('textos.pagaré'))
		];	

		if($type){
			if(isset($data[$type])){
				return ucfirst($data[$type]);
			}else{
				return false;
			}
		}else{
			return $data;
		}
	}

	/**
	 * 3.37. Estados de pagos.
	 */
	public static function paymentStatus($status = false){
		$data = [
			'A' => ucfirst(trans('textos.aprobado')),
			'C' => ucfirst(trans('textos.efectivo')),
			'N' =>  ucfirst(trans('textos.pagado_no')),
			'R' => 'R'
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}
	}

	/**
	 * 3.38. Estados de remesas contables.
	 */
	public static function remittanceStatus($status = false){
		$data = [
			1 => trans('textos.abierta'),
			10 => trans('textos.enviada'),
			20 => trans('textos.cobrado')
		];
		if($status){
			return ucfirst($data[$status]);
		}else{
			return $data;
		}	
	}

	/**
	 * 4.1. Truncar texto.
	 */
	public static function truncate($text, $chars = 120){
	    if(strlen($text) > $chars){
	        $text = $text.' ';
	        $text = substr($text, 0, $chars);
	        $text = substr($text, 0, strrpos($text ,' '));
	        $text = $text.'...';
	    }
	    return $text;
	}

	/**
	 * 4.2. Slug string para archivos.
	 */
	public static function slugFiles($String, $separator = false, $lower = true){
		$String = trim($String);
		if($lower){
			$String = strtolower($String);
		}
	    $patron = array(
	    	'/ä/' => 'a',
	    	'/á/' => 'a',
	    	'/à/' => 'a',
	    	'/â/' => 'a',
	    	'/ã/' => 'a',
	    	'/ª/' => 'a',
	    	'/ë/' => 'e',
	    	'/é/' => 'e',
	    	'/è/' => 'e',
	    	'/ê/' => 'e',
	    	'/ï/' => 'i',
	    	'/í/' => 'i',
	    	'/ì/' => 'i',
	    	'/î/' => 'i',
	    	'/ö/' => 'o',
	    	'/ó/' => 'o',
	    	'/ò/' => 'o',
	    	'/ô/' => 'o',
	    	'/õ/' => 'o',
	    	'/º/' => 'o',
	    	'/ü/' => 'u',
	    	'/ú/' => 'u',
	    	'/ù/' => 'u',
	    	'/û/' => 'u',
	    	'/Á/' => 'a',
	        '/À/' => 'a',
	        '/Â/' => 'a',
	        '/Ã/' => 'a',
	        '/Ä/' => 'a',
	        '/É/' => 'e',
	        '/È/' => 'e',
	        '/Ê/' => 'e',
	        '/Ë/' => 'e',
	        '/Í/' => 'i',
	        '/Ì/' => 'i',
	        '/Î/' => 'i',
	        '/Ï/' => 'i',
	        '/Ó/' => 'o',
	        '/Ò/' => 'o',
	        '/Ô/' => 'o',
	        '/Õ/' => 'o',
	        '/Ö/' => 'o',
	        '/Ú/' => 'u',
	        '/Ù/' => 'u',
	        '/Û/' => 'u',
	        '/Ü/' => 'u'
	    );
	    $String = preg_replace(array_keys($patron), array_values($patron), $String);
	    $String = str_replace("/","",$String);
	    //$String = str_replace(".","",$String);
	    $String = str_replace(",","",$String);
	    $String = str_replace("\\","",$String);
	    $String = str_replace("'","",$String);
	    $String = str_replace("\"","",$String);
	    $String = str_replace("+","",$String);
	    $String = str_replace("ç","c",$String);
	    $String = str_replace("Ç","C",$String);
	    $String = str_replace("ñ","n",$String);
	    $String = str_replace("Ñ","n",$String);
	    $String = str_replace("Ý","Y",$String);
	    $String = str_replace("ý","y",$String);
	    $String = str_replace(" ", "-",$String);
	    $String = str_replace("#","",$String);
	    $String = str_replace("(","",$String);
	    $String = str_replace(")","",$String);

	    if($separator){
	    	$String = str_replace(" ", $separator,$String);
	    }else{
	    	$String = str_replace(" ", "-",$String);	
	    }
	    return $String;
	}

	/**
	 * 4.3. Customizar CIF.
	 */
	public static function setCif($cif){
		$first_char = substr($cif, 0, 1);
    	$end_char = substr($cif, 1);
    	return $first_char.' '.$end_char;
	}

	/**
	 * 4.3.1. Setear NIF.
	 */
	public static function cleanNif($nif){
		$nif = str_replace("-","",$nif);
		$nif = str_replace(" ","",$nif);
		$nif = strtoupper($nif);

		return $nif;
	}

	/**
	 * 4.4. Formato numérico para decimales.
	 */
	public static function setNumericFloat($num){
		if(strpos($num, ',') !== false){
			$num = str_replace(',','.',$num);
		}
		return $num;
	}

	/**
	 * 4.5. Customizar strings.
	 */
	public static function customString($string, $num, $character){
		$arr = str_split($string, $num);
		return implode($character, $arr);
	}

	/**
	 * 4.6. Cadena serializada.
	 */
	public static function isSerialized($string) {
    	return (@unserialize($string) !== false || $string == 'b:0;');
	}

	/**
	 * 4.7. Detectar valores repetidos en un array.
	 */
	public static function getRepeatValues($data){
		$unique_values = array_unique($data);
		$comunes = array_diff_assoc($data, $unique_values);
		return $comunes;
	}

	/**
	 * 5.1. Allowed Image Mime Types.
	 */
	public static function imgMimeTypes($type = false){
		$data = ['image/jpeg','image/gif','image/png','image/bmp','image/svg+xml'];
		if($type){
			return $data[$type];
		}else{
			return $data;
		}
	}	

	/**
	 * 5.2. Extensiones según Mime Types.
	 */
	public static function mimeExtension($mime = false){
		$data = [
			"application/vnd.lotus-1-2-3" => ".123",
			"text/vnd.in3d.3dml" => ".3dml",
			"video/3gpp2" => ".3g2",
			"video/3gpp" => ".3gp",
			"application/octet-stream" => ".a",
			"application/x-authorware-bin" => ".aab",
			"audio/x-aac" => ".aac",
			"application/x-authorware-map" => ".aam",
			"application/x-authorware-seg" => ".aas",
			"application/x-abiword" => ".abw",
			"application/vnd.americandynamics.acc" => ".acc",
			"application/x-ace-compressed" => ".ace", 
			"application/vnd.acucobol" => ".acu", 
			"application/vnd.acucorp" => ".acutc", 
			"audio/adpcm" => ".adp", 
			"application/vnd.audiograph" => ".aep", 
			"application/x-font-type1" => ".afm", 
			"application/vnd.ibm.modcap" => ".afp", 
			"application/postscript" => ".ai", 
			"audio/x-aiff" => ".aif", 
			"audio/x-aiff" => ".aifc", 
			"audio/x-aiff" => ".aiff", 
			"application/vnd.adobe.air-application-installer-package+zip" => ".air", 
			"application/vnd.amiga.ami" => ".ami", 
			"application/vnd.android.package-archive" => ".apk", 
			"application/x-ms-application" => ".application", 
			"application/vnd.lotus-approach" => ".apr", 
			"application/pgp-signature" => ".asc", 
			"video/x-ms-asf" => ".asf", 
			"text/x-asm" => ".asm", 
			"application/vnd.accpac.simply.aso" => ".aso", 
			"video/x-ms-asf" => ".asx", 
			"application/vnd.acucorp" => ".atc", 
			"application/atom+xml" => ".atom", 
			"application/atomcat+xml" => ".atomcat", 
			"application/atomsvc+xml" => ".atomsvc", 
			"application/vnd.antix.game-component" => ".atx", 
			"audio/basic" => ".au", 
			"video/x-msvideo" => ".avi", 
			"application/applixware" => ".aw", 
			"application/vnd.airzip.filesecure.azf" => ".azf", 
			"application/vnd.airzip.filesecure.azs" => ".azs", 
			"application/vnd.amazon.ebook" => ".azw", 
			"application/x-msdownload" => ".bat", 
			"application/x-bcpio" => ".bcpio", 
			"application/x-font-bdf" => ".bdf", 
			"application/vnd.syncml.dm+wbxml" => ".bdm", 
			"application/vnd.fujitsu.oasysprs" => ".bh2", 
			"application/octet-stream" => ".bin", 
			"application/vnd.bmi" => ".bmi", 
			"image/bmp" => ".bmp", 
			"application/vnd.framemaker" => ".book", 
			"application/vnd.previewsystems.box" => ".box", 
			"application/x-bzip2" => ".boz", 
			"application/octet-stream" => ".bpk", 
			"image/prs.btif" => ".btif", 
			"application/x-bzip" => ".bz", 
			"application/x-bzip2" => ".bz2", 
			"text/x-c" => ".c", 
			"application/vnd.clonk.c4group" => ".c4d", 
			"application/vnd.clonk.c4group" => ".c4f", 
			"application/vnd.clonk.c4group" => ".c4g", 
			"application/vnd.clonk.c4group" => ".c4p", 
			"application/vnd.clonk.c4group" => ".c4u", 
			"application/vnd.ms-cab-compressed" => ".cab", 
			"application/vnd.curl.car" => ".car", 
			"application/vnd.ms-pki.seccat" => ".cat", 
			"text/x-c" => ".cc", 
			"application/x-director" => ".cct", 
			"application/ccxml+xml" => ".ccxml", 
			"application/vnd.contact.cmsg" => ".cdbcmsg", 
			"application/x-netcdf" => ".cdf", 
			"application/vnd.mediastation.cdkey" => ".cdkey", 
			"chemical/x-cdx" => ".cdx", 
			"application/vnd.chemdraw+xml" => ".cdxml", 
			"application/vnd.cinderella" => ".cdy", 
			"application/pkix-cert" => ".cer", 
			"image/cgm" => ".cgm", 
			"application/x-chat" => ".chat", 
			"application/vnd.ms-htmlhelp" => ".chm", 
			"application/vnd.kde.kchart" => ".chrt", 
			"chemical/x-cif" => ".cif", 
			"application/vnd.anser-web-certificate-issue-initiation" => ".cii", 
			"application/vnd.ms-artgalry" => ".cil", 
			"application/vnd.claymore" => ".cla", 
			"application/java-vm" => ".class", 
			"application/vnd.crick.clicker.keyboard" => ".clkk", 
			"application/vnd.crick.clicker.palette" => ".clkp", 
			"application/vnd.crick.clicker.template" => ".clkt", 
			"application/vnd.crick.clicker.wordbank" => ".clkw", 
			"application/vnd.crick.clicker" => ".clkx", 
			"application/x-msclip" => ".clp", 
			"application/vnd.cosmocaller" => ".cmc", 
			"chemical/x-cmdf" => ".cmdf", 
			"chemical/x-cml" => ".cml", 
			"application/vnd.yellowriver-custom-menu" => ".cmp", 
			"image/x-cmx" => ".cmx", 
			"application/vnd.rim.cod" => ".cod", 
			"application/x-msdownload" => ".com", 
			"application/x-cpio" => ".cpio", 
			"text/x-c" => ".cpp", 
			"application/mac-compactpro" => ".cpt", 
			"application/x-mscardfile" => ".crd", 
			"application/pkix-crl" => ".crl", 
			"application/x-x509-ca-cert" => ".crt", 
			"application/x-csh" => ".csh", 
			"chemical/x-csml" => ".csml", 
			"application/vnd.commonspace" => ".csp", 
			"text/css" => ".css", 
			"application/x-director" => ".cst", 
			"text/csv" => ".csv", 
			"application/cu-seeme" => ".cu", 
			"text/vnd.curl" => ".curl", 
			"application/prs.cww" => ".cww", 
			"application/x-director" => ".cxt", 
			"text/x-c" => ".cxx", 
			"application/vnd.mobius.daf" => ".daf", 
			"application/vnd.fdsn.seed" => ".dataless", 
			"application/davmount+xml" => ".davmount", 
			"application/x-director" => ".dcr", 
			"text/vnd.curl.dcurl" => ".dcurl", 
			"application/vnd.oma.dd2+xml" => ".dd2", 
			"application/vnd.fujixerox.ddd" => ".ddd", 
			"application/x-debian-package" => ".deb", 
			"application/octet-stream" => ".deploy", 
			"application/x-x509-ca-cert" => ".der", 
			"application/vnd.dreamfactory" => ".dfac", 
			"text/x-c" => ".dic", 
			"application/x-director" => ".dir", 
			"application/vnd.mobius.dis" => ".dis", 
			"application/octet-stream" => ".dist", 
			"application/octet-stream" => ".distz", 
			"image/vnd.djvu" => ".djv", 
			"image/vnd.djvu" => ".djvu", 
			"application/x-msdownload" => ".dll", 
			"application/octet-stream" => ".dmg", 
			"application/octet-stream" => ".dms", 
			"application/vnd.dna" => ".dna", 
			"application/msword" => ".doc", 
			"application/vnd.ms-word.document.macroenabled.12" => ".docm", 
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document" => ".docx", 
			"application/msword" => ".dot", 
			"application/vnd.ms-word.template.macroenabled.12" => ".dotm", 
			"application/vnd.openxmlformats-officedocument.wordprocessingml.template" => ".dotx", 
			"application/vnd.osgi.dp" => ".dp", 
			"application/vnd.dpgraph" => ".dpg", 
			"text/prs.lines.tag" => ".dsc", 
			"application/x-dtbook+xml" => ".dtb", 
			"application/xml-dtd" => ".dtd", 
			"audio/vnd.dts" => ".dts", 
			"audio/vnd.dts.hd" => ".dtshd", 
			"application/octet-stream" => ".dump", 
			"application/x-dvi" => ".dvi", 
			"model/vnd.dwf" => ".dwf", 
			"image/vnd.dwg" => ".dwg", 
			"image/vnd.dxf" => ".dxf", 
			"application/vnd.spotfire.dxp" => ".dxp", 
			"application/x-director" => ".dxr", 
			"audio/vnd.nuera.ecelp4800" => ".ecelp4800", 
			"audio/vnd.nuera.ecelp7470" => ".ecelp7470", 
			"audio/vnd.nuera.ecelp9600" => ".ecelp9600", 
			"application/ecmascript" => ".ecma", 
			"application/vnd.novadigm.edm" => ".edm", 
			"application/vnd.novadigm.edx" => ".edx", 
			"application/vnd.picsel" => ".efif", 
			"application/vnd.pg.osasli" => ".ei6", 
			"application/octet-stream" => ".elc", 
			"message/rfc822" => ".eml", 
			"application/emma+xml" => ".emma", 
			"audio/vnd.digital-winds" => ".eol", 
			"application/vnd.ms-fontobject" => ".eot", 
			"application/postscript" => ".eps", 
			"application/epub+zip" => ".epub", 
			"application/vnd.eszigno3+xml" => ".es3", 
			"application/vnd.epson.esf" => ".esf", 
			"application/vnd.eszigno3+xml" => ".et3", 
			"text/x-setext" => ".etx", 
			"application/x-msdownload" => ".exe", 
			"application/vnd.novadigm.ext" => ".ext", 
			"application/andrew-inset" => ".ez", 
			"application/vnd.ezpix-album" => ".ez2", 
			"application/vnd.ezpix-package" => ".ez3", 
			"text/x-fortran" => ".f", 
			"video/x-f4v" => ".f4v", 
			"text/x-fortran" => ".f77", 
			"text/x-fortran" => ".f90", 
			"image/vnd.fastbidsheet" => ".fbs", 
			"application/vnd.fdf" => ".fdf", 
			"application/vnd.denovo.fcselayout-link" => ".fe_launch", 
			"application/vnd.fujitsu.oasysgp" => ".fg5", 
			"application/x-director" => ".fgd", 
			"image/x-freehand" => ".fh", 
			"image/x-freehand" => ".fh4", 
			"image/x-freehand" => ".fh5", 
			"image/x-freehand" => ".fh7", 
			"image/x-freehand" => ".fhc", 
			"application/x-xfig" => ".fig", 
			"video/x-fli" => ".fli", 
			"application/vnd.micrografx.flo" => ".flo", 
			"video/x-flv" => ".flv", 
			"application/vnd.kde.kivio" => ".flw", 
			"text/vnd.fmi.flexstor" => ".flx", 
			"text/vnd.fly" => ".fly", 
			"application/vnd.framemaker" => ".fm", 
			"application/vnd.frogans.fnc" => ".fnc", 
			"text/x-fortran" => ".for", 
			"image/vnd.fpx" => ".fpx", 
			"application/vnd.framemaker" => ".frame", 
			"application/vnd.fsc.weblaunch" => ".fsc", 
			"image/vnd.fst" => ".fst", 
			"application/vnd.fluxtime.clip" => ".ftc", 
			"application/vnd.anser-web-funds-transfer-initiation" => ".fti", 
			"video/vnd.fvt" => ".fvt", 
			"application/vnd.fuzzysheet" => ".fzs", 
			"image/g3fax" => ".g3", 
			"application/vnd.groove-account" => ".gac", 
			"model/vnd.gdl" => ".gdl", 
			"application/vnd.dynageo" => ".geo", 
			"application/vnd.geometry-explorer" => ".gex", 
			"application/vnd.geogebra.file" => ".ggb", 
			"application/vnd.geogebra.tool" => ".ggt", 
			"application/vnd.groove-help" => ".ghf", 
			"image/gif" => ".gif", 
			"application/vnd.groove-identity-message" => ".gim", 
			"application/vnd.gmx" => ".gmx", 
			"application/x-gnumeric" => ".gnumeric", 
			"application/vnd.flographit" => ".gph", 
			"application/vnd.grafeq" => ".gqf", 
			"application/vnd.grafeq" => ".gqs", 
			"application/srgs" => ".gram", 
			"application/vnd.geometry-explorer" => ".gre", 
			"application/vnd.groove-injector" => ".grv", 
			"application/srgs+xml" => ".grxml", 
			"application/x-font-ghostscript" => ".gsf", 
			"application/x-gtar" => ".gtar", 
			"application/vnd.groove-tool-message" => ".gtm", 
			"model/vnd.gtw" => ".gtw", 
			"text/vnd.graphviz" => ".gv", 
			"application/x-gzip" => ".gz", 
			"text/x-c" => ".h", 
			"video/h261" => ".h261", 
			"video/h263" => ".h263", 
			"video/h264" => ".h264", 
			"application/vnd.hbci" => ".hbci", 
			"application/x-hdf" => ".hdf", 
			"text/x-c" => ".hh", 
			"application/winhlp" => ".hlp", 
			"application/vnd.hp-hpgl" => ".hpgl", 
			"application/vnd.hp-hpid" => ".hpid", 
			"application/vnd.hp-hps" => ".hps", 
			"application/mac-binhex40" => ".hqx", 
			"application/vnd.kenameaapp" => ".htke", 
			"text/html" => ".htm", 
			"text/html" => ".html", 
			"application/vnd.yamaha.hv-dic" => ".hvd", 
			"application/vnd.yamaha.hv-voice" => ".hvp", 
			"application/vnd.yamaha.hv-script" => ".hvs", 
			"application/vnd.iccprofile" => ".icc", 
			"x-conference/x-cooltalk" => ".ice", 
			"application/vnd.iccprofile" => ".icm", 
			"image/x-icon" => ".ico", 
			"text/calendar" => ".ics", 
			"image/ief" => ".ief", 
			"text/calendar" => ".ifb", 
			"application/vnd.shana.informed.formdata" => ".ifm", 
			"model/iges" => ".iges", 
			"application/vnd.igloader" => ".igl", 
			"model/iges" => ".igs", 
			"application/vnd.micrografx.igx" => ".igx", 
			"application/vnd.shana.informed.interchange" => ".iif", 
			"application/vnd.accpac.simply.imp" => ".imp", 
			"application/vnd.ms-ims" => ".ims", 
			"application/vnd.shana.informed.package" => ".ipk", 
			"application/vnd.ibm.rights-management" => ".irm", 
			"application/vnd.irepository.package+xml" => ".irp", 
			"application/octet-stream" => ".iso", 
			"application/vnd.shana.informed.formtemplate" => ".itp", 
			"application/vnd.immervision-ivp" => ".ivp", 
			"application/vnd.immervision-ivu" => ".ivu", 
			"text/vnd.sun.j2me.app-descriptor" => ".jad", 
			"application/vnd.jam" => ".jam", 
			"application/java-archive" => ".jar", 
			"text/x-java-source" => ".java", 
			"application/vnd.jisp" => ".jisp", 
			"application/vnd.hp-jlyt" => ".jlt", 
			"application/x-java-jnlp-file" => ".jnlp", 
			"application/vnd.joost.joda-archive" => ".joda", 
			"image/jpeg" => ".jpg",
			"image/jpeg" => ".jpeg", 
			"image/jpe" => ".jpe",
			"video/jpm" => ".jpgm", 
			"video/jpm" => ".jpm", 
			"application/javascript" => ".js", 
			"application/json" => ".json", 
			"audio/midi" => ".kar", 
			"application/vnd.kde.karbon" => ".kfo", 
			"application/vnd.kde.kformula" => ".kia", 
			"application/vnd.kidspiration" => ".kil", 
			"application/x-killustrator" => ".kml", 
			"application/vnd.google-earth.kml+xml" => ".kmz", 
			"application/vnd.google-earth.kmz" => ".kne", 
			"application/vnd.kinar" => ".knp", 
			"application/vnd.kinar" => ".kon", 
			"application/vnd.kde.kontour" => ".kpr", 
			"application/vnd.kde.kpresenter" => ".kpt", 
			"application/vnd.kde.kpresenter" => ".ksh", 
			"application/vnd.kde.kspread" => ".ktr", 
			"application/vnd.kahootz" => ".ktz", 
			"application/vnd.kahootz" => ".kwd", 
			"application/vnd.kde.kword" => ".kwt", 
			"application/x-latex" => ".latex", 
			"application/vnd.llamagraphics.life-balance.desktop" => ".lbd", 
			"application/vnd.llamagraphics.life-balance.exchange+xml" => ".lbe", 
			"application/vnd.hhe.lesson-player" => ".les", 
			"application/octet-stream" => ".lha", 
			"application/vnd.route66.link66+xml" => ".link66", 
			"application/vnd.ibm.modcap" => ".list3820", 
			"application/vnd.ibm.modcap" => ".listafp", 
			"application/lost+xml" => ".lostxml", 
			"application/octet-stream" => ".lrf", 
			"application/vnd.ms-lrm" => ".lrm", 
			"application/vnd.frogans.ltf" => ".ltf", 
			"audio/vnd.lucent.voice" => ".lvp", 
			"application/vnd.lotus-wordpro" => ".lwp", 
			"application/octet-stream" => ".lzh", 
			"application/x-msmediaview" => ".m13", 
			"application/x-msmediaview" => ".m14", 
			"video/mpeg" => ".m1v", 
			"audio/mpeg" => ".m2a", 
			"video/mpeg" => ".m2v", 
			"audio/mpeg" => ".m3a", 
			"audio/x-mpegurl" => ".m3u", 
			"video/vnd.mpegurl" => ".m4u", 
			"video/x-m4v" => ".m4v", 
			"application/mathematica" => ".ma", 
			"application/vnd.ecowin.chart" => ".mag", 
			"application/vnd.framemaker" => ".maker", 
			"text/troff" => ".man", 
			"application/mathml+xml" => ".mathml", 
			"application/mathematica" => ".mb", 
			"application/vnd.mobius.mbk" => ".mbk", 
			"application/mbox" => ".mbox", 
			"application/vnd.medcalcdata" => ".mc1", 
			"application/vnd.mcd" => ".mcd", 
			"text/vnd.curl.mcurl" => ".mcurl", 
			"application/x-msaccess" => ".mdb", 
			"image/vnd.ms-modi" => ".mdi", 
			"text/troff" => ".me", 
			"model/mesh" => ".mesh", 
			"application/vnd.mfmp" => ".mfm", 
			"application/vnd.proteus.magazine" => ".mgz", 
			"message/rfc822" => ".mht", 
			"message/rfc822" => ".mhtml", 
			"audio/midi" => ".mid", 
			"audio/midi" => ".midi", 
			"application/vnd.mif" => ".mif", 
			"message/rfc822" => ".mime", 
			"video/mj2" => ".mj2", 
			"video/mj2" => ".mjp2", 
			"application/vnd.dolby.mlp" => ".mlp", 
			"application/vnd.chipnuts.karaoke-mmd" => ".mmd", 
			"application/vnd.smaf" => ".mmf", 
			"image/vnd.fujixerox.edmics-mmr" => ".mmr", 
			"application/x-msmoney" => ".", 
			"application/x-mobipocket-ebook" => ".mobi", 
			"video/quicktime" => ".mny", 
			"video/x-sgi-movie" => ".mov", 
			"audio/mpeg" => ".movie", 
			"audio/mpeg" => ".mp2", 
			"audio/mpeg" => ".mp2a", 
			"video/mp3" => ".mp3", 
			"audio/mp4" => ".mp4", 
			"application/mp4" => ".mp4a", 
			"video/mp4" => ".mp4s", 
			"video/mpeg" => ".mp4v", 
			"application/vnd.mophun.certificate" => ".mpc", 
			"video/mpeg" => ".mpe", 
			"video/mpeg" => ".mpeg", 
			"video/mpeg" => ".mpg", 
			"video/mp4" => ".mpg4"	, 
			"audio/mpeg" => ".mpga", 
			"application/vnd.apple.installer+xml" => ".mpkg", 
			"application/vnd.blueice.multipass" => ".mpm", 
			"application/vnd.mophun.application" => ".mpn", 
			"application/vnd.ms-project" => ".mpp"	, 
			"application/vnd.ms-project" => ".mpt", 
			"application/vnd.ibm.minipay" => ".mpy", 
			"application/vnd.mobius.mqy" => ".mqy", 
			"application/marc" => ".mrc", 
			"text/troff" => ".ms", 
			"application/mediaservercontrol+xml" => ".mscml", 
			"application/vnd.fdsn.mseed" => ".mseed", 
			"application/vnd.mseq" => ".mseq", 
			"application/vnd.epson.msf" => ".msf", 
			"model/mesh" => ".msh", 
			"application/x-msdownload" => ".msi", 
			"application/vnd.mobius.msl" => ".msl", 
			"application/vnd.muvee.style" => ".msty", 
			"model/vnd.mts" => ".mts", 
			"application/vnd.musician" => ".mus", 
			"application/vnd.recordare.musicxml+xml" => ".musicxml"	, 
			"application/x-msmediaview" => ".mvb", 
			"application/vnd.mfer" => ".mwf", 
			"application/mxf" => ".mxf", 
			"application/vnd.recordare.musicxml" => ".mxl", 
			"application/xv+xml" => ".mxml", 
			"application/vnd.triscape.mxs" => ".mxs", 
			"video/vnd.mpegurl" => ".mxu", 
			"application/vnd.nokia.n-gage.symbian.install" => ".n-gage", 
			"application/mathematica" => ".nb", 
			"application/x-netcdf" => ".nc", 
			"application/x-dtbncx+xml" => ".ncx", 
			"application/vnd.nokia.n-gage.data" => ".ngdat", 
			"application/vnd.neurolanguage.nlu" => ".nlu", 
			"application/vnd.enliven" => ".nml", 
			"application/vnd.noblenet-directory" => ".nnd", 
			"application/vnd.noblenet-sealer" => ".nns", 
			"application/vnd.noblenet-web" => ".nnw", 
			"image/vnd.net-fpx" => ".npx", 
			"application/vnd.lotus-notes" => ".nsf", 
			"message/rfc822" => ".nws", 
			"application/octet-stream" => ".o", 
			"application/vnd.fujitsu.oasys2" => ".oa2", 
			"application/vnd.fujitsu.oasys3" => ".oa3", 
			"application/vnd.fujitsu.oasys" => ".oas", 
			"application/x-msbinder" => ".obd", 
			"application/octet-stream" => ".obj", 
			"application/oda" => ".oda", 
			"application/vnd.oasis.opendocument.database" => ".odb", 
			"application/vnd.oasis.opendocument.chart" => ".odc", 
			"application/vnd.oasis.opendocument.formula" => ".odf", 
			"application/vnd.oasis.opendocument.formula-template" => ".odft", 
			"application/vnd.oasis.opendocument.graphics" => ".odg", 
			"application/vnd.oasis.opendocument.image" => ".odi", 
			"application/vnd.oasis.opendocument.presentation" => ".odp", 
			"application/vnd.oasis.opendocument.spreadsheet" => ".ods", 
			"application/vnd.oasis.opendocument.text" => ".odt", 
			"audio/ogg" => ".oga", 
			"audio/ogg" => ".ogg", 
			"video/ogg" => ".ogv", 
			"application/ogg" => ".ogx", 
			"application/onenote" => ".onepkg", 
			"application/onenote" => ".onetmp", 
			"application/onenote" => ".onetoc", 
			"application/onenote" => ".onetoc2", 
			"application/oebps-package+xml" => ".opf", 
			"application/vnd.palm" => ".oprc", 
			"application/vnd.lotus-organizer" => ".org", 
			"application/vnd.yamaha.openscoreformat" => ".osf", 
			"application/vnd.yamaha.openscoreformat.osfpvg+xml" => ".osfpvg", 
			"application/vnd.oasis.opendocument.chart-template" => ".otc", 
			"application/x-font-otf" => ".otf", 
			"application/vnd.oasis.opendocument.graphics-template" => ".otg", 
			"application/vnd.oasis.opendocument.text-web" => ".oth", 
			"application/vnd.oasis.opendocument.image-template" => ".oti", 
			"application/vnd.oasis.opendocument.text-master" => ".otm", 
			"application/vnd.oasis.opendocument.presentation-template" => ".otp", 
			"application/vnd.oasis.opendocument.spreadsheet-template" => ".ots", 
			"application/vnd.oasis.opendocument.text-template" => ".ott", 
			"application/vnd.openofficeorg.extension" => ".oxt", 
			"text/x-pascal" => ".p", 
			"application/pkcs10" => ".p10", 
			"application/x-pkcs12" => ".p12", 
			"application/x-pkcs7-certificates" => ".p7b", 
			"application/pkcs7-mime" => ".p7c", 
			"application/pkcs7-mime" => ".p7m", 
			"application/x-pkcs7-certreqresp" => ".p7r", 
			"application/pkcs7-signature" => ".p7s", 
			"text/x-pascal" => ".pas", 
			"application/vnd.powerbuilder6" => ".pbd", 
			"image/x-portable-bitmap" => ".pbm", 
			"application/x-font-pcf" => ".pcf", 
			"application/vnd.hp-pcl" => ".pcl", 
			"application/vnd.hp-pclxl" => ".pclxl", 
			"image/x-pict" => ".pct", 
			"application/vnd.curl.pcurl" => ".pcurl", 
			"image/x-pcx" => ".pcx", 
			"application/vnd.palm" => ".pdb", 
			"application/pdf" => ".pdf", 
			"application/x-font-type1" => ".pfa", 
			"application/x-font-type1" => ".pfb", 
			"application/x-font-type1" => ".pfm", 
			"application/font-tdpfr" => ".pfr", 
			"application/x-pkcs12" => ".pfx", 
			"image/x-portable-graymap" => ".pgm", 
			"application/x-chess-pgn" => ".pgn", 
			"application/pgp-encrypted" => ".pgp", 
			"image/x-pict" => ".pic", 
			"application/octet-stream" => ".pkg", 
			"application/pkixcmp" => ".pki", 
			"application/pkix-pkipath" => ".pkipath", 
			"application/vnd.3gpp.pic-bw-large" => ".plb", 
			"application/vnd.mobius.plc" => ".plc", 
			"application/vnd.pocketlearn" => ".plf", 
			"application/pls+xml" => ".pls", 
			"application/vnd.ctc-posml" => ".pml", 
			"image/png" => ".png", 
			"image/x-portable-anymap" => ".pnm", 
			"application/vnd.macports.portpkg" => ".portpkg", 
			"application/vnd.ms-powerpoint" => ".pot", 
			"application/vnd.ms-powerpoint.template.macroenabled.12" => ".potm", 
			"application/vnd.openxmlformats-officedocument.presentationml.template" => ".potx", 
			"application/vnd.ms-powerpoint" => ".ppa", 
			"application/vnd.ms-powerpoint.addin.macroenabled.12" => ".ppam", 
			"application/vnd.cups-ppd" => ".ppd", 
			"image/x-portable-pixmap" => ".ppm", 
			"application/vnd.ms-powerpoint" => ".pps", 
			"application/vnd.ms-powerpoint.slideshow.macroenabled.12" => ".ppsm", 
			"application/vnd.openxmlformats-officedocument.presentationml.slideshow" => ".ppsx", 
			"application/vnd.ms-powerpoint" => ".ppt", 
			"application/vnd.ms-powerpoint.presentation.macroenabled.12" => ".pptm", 
			"application/vnd.openxmlformats-officedocument.presentationml.presentation" => ".pptx", 
			"application/vnd.palm" => ".pqa", 
			"application/x-mobipocket-ebook" => ".prc", 
			"application/vnd.lotus-freelance" => ".pre", 
			"application/pics-rules" => ".prf", 
			"application/postscript" => ".ps", 
			"application/vnd.3gpp.pic-bw-small" => ".psb", 
			"image/vnd.adobe.photoshop" => ".psd", 
			"application/x-font-linux-psf" => ".psf", 
			"application/vnd.pvi.ptid1" => ".ptid", 
			"application/x-mspublisher" => ".pub", 
			"application/vnd.3gpp.pic-bw-var" => ".pvb", 
			"application/vnd.3m.post-it-notes" => ".pwn", 
			"application/vnd.ms-powerpoint" => "pwz.", 
			"text/x-python" => ".py", 
			"audio/vnd.ms-playready.media.pya" => "pya.", 
			"application/x-python-code" => ".pyc", 
			"application/x-python-code" => ".pyo", 
			"video/vnd.ms-playready.media.pyv" => ".pyv", 
			"application/vnd.epson.quickanime" => ".qam", 
			"application/vnd.intu.qbo" => ".qbo", 
			"application/vnd.intu.qfx" => ".qfx", 
			"application/vnd.publishare-delta-tree" => ".qps", 
			"video/quicktime" => ".qt", 
			"application/vnd.quark.quarkxpress" => "qwd.", 
			"application/vnd.quark.quarkxpress" => "qwt.", 
			"application/vnd.quark.quarkxpress" => ".qxb", 
			"application/vnd.quark.quarkxpress" => ".qxd", 
			"application/vnd.quark.quarkxpress" => ".qxl", 
			"application/vnd.quark.quarkxpress" => ".qxt", 
			"audio/x-pn-realaudio" => ".ra", 
			"audio/x-pn-realaudio" => ".ram", 
			"application/x-rar-compressed" => ".rar", 
			"image/x-cmu-raster" => ".ras", 
			"application/vnd.ipunplugged.rcprofile" => ".rcprofile", 
			"application/rdf+xml" => ".rdf", 
			"application/vnd.data-vision.rdz" => ".rdz", 
			"application/vnd.businessobjects" => ".rep", 
			"application/x-dtbresource+xml" => ".res", 
			"image/x-rgb" => ".rgb", 
			"application/reginfo+xml" => ".rif", 
			"application/resource-lists+xml" => ".rl", 
			"image/vnd.fujixerox.edmics-rlc" => ".rlc", 
			"application/resource-lists-diff+xml" => ".rld", 
			"application/vnd.rn-realmedia" => ".rm", 
			"audio/midi" => ".rmi", 
			"audio/x-pn-realaudio-plugin" => ".rmp", 
			"application/vnd.jcp.javame.midlet-rms" => ".rms", 
			"application/relax-ng-compact-syntax" => ".rnc", 
			"text/troff" => ".roff", 
			"application/x-rpm" => ".rpm", 
			"application/vnd.nokia.radio-presets" => ".rpss", 
			"application/vnd.nokia.radio-preset" => ".rpst", 
			"application/sparql-query" => ".rq", 
			"application/rls-services+xml" => ".rs", 
			"application/rsd+xml" => ".rsd", 
			"application/rss+xml" => ".rss", 
			"application/rtf" => ".rtf", 
			"text/richtext" => ".rtx", 
			"text/x-asm" => ".s", 
			"application/vnd.yamaha.smaf-audio" => ".saf", 
			"application/sbml+xml" => ".sbml", 
			"application/vnd.ibm.secure-container" => ".sc", 
			"application/x-msschedule" => ".scd", 
			"application/vnd.lotus-screencam" => ".scm", 
			"application/scvp-cv-request" => ".scq", 
			"application/scvp-cv-response" => ".scs", 
			"text/vnd.curl.scurl" => ".scurl", 
			"application/vnd.stardivision.draw" => ".sda", 
			"application/vnd.stardivision.calc" => ".sdc", 
			"application/vnd.stardivision.impress" => ".sdd", 
			"application/vnd.solent.sdkm+xml" => ".sdkd", 
			"application/vnd.solent.sdkm+xml" => ".sdkm", 
			"application/sdp" => ".sdp", 
			"application/vnd.stardivision.writer" => ".sdw", 
			"application/vnd.seemail" => ".see", 
			"application/vnd.fdsn.seed" => ".seed", 
			"application/vnd.sema" => ".sema", 
			"application/vnd.semd" => ".semd", 
			"application/vnd.semf" => ".semf", 
			"application/java-serialized-object" => ".ser", 
			"application/set-payment-initiation" => ".setpay", 
			"application/set-registration-initiation" => ".setreg", 
			"application/vnd.hydrostatix.sof-data" => ".sfd-hdstx", 
			"application/vnd.spotfire.sfs" => ".sfs", 
			"application/vnd.stardivision.writer-global" => ".sgl", 
			"text/sgml" => ".sgm", 
			"text/sgml" => ".sgml", 
			"application/x-sh" => ".sh", 
			"application/x-shar" => ".shar", 
			"application/shf+xml" => ".shf", 
			"text/vnd.wap.si" => ".si", 
			"application/vnd.wap.sic" => ".sic", 
			"application/pgp-signature" => ".sig", 
			"model/mesh" => ".silo", 
			"application/vnd.symbian.install" => ".sis", 
			"application/vnd.symbian.install" => ".sisx", 
			"application/x-stuffit" => ".sit", 
			"application/x-stuffitx" => ".sitx", 
			"application/vnd.koan" => ".skd", 
			"application/vnd.koan" => ".skm", 
			"application/vnd.koan" => ".skp", 
			"application/vnd.koan" => ".skt", 
			"text/vnd.wap.sl" => ".sl", 
			"application/vnd.wap.slc" => ".slc", 
			"application/vnd.ms-powerpoint.slide.macroenabled.12" => ".sldm", 
			"application/vnd.openxmlformats-officedocument.presentationml.slide" => ".sldx", 
			"application/vnd.epson.salt" => ".slt", 
			"application/vnd.stardivision.math" => ".smf", 
			"application/smil+xml" => ".smi", 
			"application/smil+xml" => ".smil", 
			"audio/basic" => ".snd", 
			"application/x-font-snf" => ".snf", 
			"application/octet-stream" => ".so", 
			"application/x-pkcs7-certificates" => ".spc", 
			"application/vnd.yamaha.smaf-phrase" => ".spf", 
			"application/x-futuresplash" => ".spl", 
			"text/vnd.in3d.spot" => ".spot", 
			"application/scvp-vp-response" => ".spp", 
			"application/scvp-vp-request" => ".spq", 
			"audio/ogg" => ".spx", 
			"application/x-wais-source" => ".src", 
			"application/sparql-results+xml" => ".srx", 
			"application/vnd.kodak-descriptor" => ".sse", 
			"application/vnd.epson.ssf" => ".ssf", 
			"application/ssml+xml" => ".ssml", 
			"application/vnd.sun.xml.calc.template" => ".stc", 
			"application/vnd.sun.xml.draw.template" => ".std", 
			"application/vnd.wt.stf" => ".stf", 
			"application/vnd.sun.xml.impress.template" => ".sti", 
			"application/hyperstudio" => ".stk", 
			"application/vnd.ms-pki.stl" => ".stl", 
			"application/vnd.pg.format" => ".str", 
			"application/vnd.sun.xml.writer.template" => ".stw", 
			"application/vnd.sus-calendar" => ".sus", 
			"application/vnd.sus-calendar" => ".susp", 
			"application/x-sv4cpio" => ".sv4cpio", 
			"application/x-sv4crc" => ".sv4crc", 
			"application/vnd.svd" => ".svd", 
			"image/svg+xml" => ".svg", 
			"image/svg+xml" => ".svgz", 
			"application/x-director" => ".swa", 
			"application/x-shockwave-flash" => ".swf", 
			"application/vnd.arastra.swi" => ".swi", 
			"application/vnd.sun.xml.calc" => ".sxc", 
			"application/vnd.sun.xml.draw" => ".sxd", 
			"application/vnd.sun.xml.writer.global" => ".sxg", 
			"application/vnd.sun.xml.impress" => ".sxi", 
			"application/vnd.sun.xml.math" => ".sxm", 
			"application/vnd.sun.xml.writer" => ".sxw", 
			"text/troff" => ".t", 
			"application/vnd.tao.intent-module-archive" => ".tao", 
			"application/x-tar" => ".tar", 
			"application/vnd.3gpp2.tcap" => ".tcap", 
			"application/x-tcl" => ".tcl", 
			"application/vnd.smart.teacher" => ".teacher", 
			"application/x-tex" => ".tex", 
			"application/x-texinfo" => ".texi", 
			"application/x-texinfo" => ".texinfo", 
			"application/x-tex-tfm" => ".tfm", 
			"application/x-gzip" => ".tgz", 
			"image/tiff" => ".tif", 
			"image/tiff" => ".tiff", 
			"application/vnd.tmobile-livetv" => ".tmo", 
			"application/x-bittorrent" => ".torrent", 
			"application/vnd.groove-tool-template" => ".tpl", 
			"application/vnd.trid.tpt" => ".tpt", 
			"text/troff" => ".tr", 
			"application/vnd.trueapp" => ".tra", 
			"application/x-msterminal" => ".trm", 
			"text/tab-separated-values" => ".tsv", 
			"application/x-font-ttf" => ".ttc", 
			"application/x-font-ttf" => ".ttf", 
			"application/vnd.simtech-mindmapper" => "twd.", 
			"application/vnd.simtech-mindmapper" => ".twds", 
			"application/vnd.genomatix.tuxedo" => ".txd", 
			"application/vnd.mobius.txf" => ".txf", 
			"text/plain" => ".txt", 
			"text/plain" => ".text", 
			"application/x-authorware-bin" => ".u32", 
			"application/x-debian-package" => ".udeb", 
			"application/vnd.ufdl" => ".ufd", 
			"application/vnd.ufdl" => ".ufdl", 
			"application/vnd.umajin" => ".umj", 
			"application/vnd.unity" => ".unityweb", 
			"application/vnd.uoml+xml" => ".uoml", 
			"text/uri-list" => ".uri", 
			"text/uri-list" => ".uris", 
			"text/uri-list" => ".urls", 
			"application/x-ustar" => ".ustar", 
			"application/vnd.uiq.theme" => ".utz", 
			"text/x-uuencode" => ".uu", 
			"application/x-cdlink" => ".vcd", 
			"text/x-vcard" => ".vcf", 
			"application/vnd.groove-vcard" => ".vcg", 
			"text/x-vcalendar" => ".vcs", 
			"application/vnd.vcx" => ".vcx", 
			"application/vnd.visionary" => ".vis", 
			"video/vnd.vivo" => ".viv", 
			"application/vnd.stardivision.writer" => ".vor", 
			"application/x-authorware-bin" => ".vox", 
			"model/vrml" => ".vrml", 
			"application/vnd.visio" => ".vsd", 
			"application/vnd.vsf" => ".vsf", 
			"application/vnd.visio" => ".vss", 
			"application/vnd.visio" => ".vst", 
			"application/vnd.visio" => ".vsw", 
			"model/vnd.vtu" => ".vtu", 
			"application/voicexml+xml" => ".vxml", 
			"application/x-director" => ".w3d", 
			"application/x-doom" => ".wad", 
			"audio/x-wav" => ".wav", 
			"audio/x-ms-wax" => ".wax", 
			"image/vnd.wap.wbmp" => "wbmp.", 
			"application/vnd.criticaltools.wbs+xml" => ".wbs", 
			"application/vnd.wap.wbxml" => ".wbxml", 
			"application/vnd.ms-works" => ".wcm", 
			"application/vnd.ms-works" => ".wdb", 
			"application/msword" => ".wiz", 
			"application/vnd.ms-works" => ".wks", 
			"video/x-ms-wm" => ".wm", 
			"audio/x-ms-wma" => ".wma", 
			"application/x-ms-wmd" => ".wmd", 
			"application/x-msmetafile" => ".wmf", 
			"text/vnd.wap.wml" => ".wml", 
			"application/vnd.wap.wmlc" => ".wmlc", 
			"text/vnd.wap.wmlscript" => ".wmls", 
			"application/vnd.wap.wmlscriptc" => ".wmlsc", 
			"video/x-ms-wmv" => ".wmv", 
			"video/x-ms-wmx" => ".wmx", 
			"application/x-ms-wmz" => ".wmz", 
			"application/vnd.wordperfect" => ".wpd", 
			"application/vnd.ms-wpl" => ".wpl", 
			"application/vnd.ms-works" => ".wps", 
			"application/vnd.wqd" => ".wqd", 
			"application/x-mswrite" => ".wri", 
			"model/vrml" => ".wrl", 
			"application/wsdl+xml" => ".wsdl", 
			"application/wspolicy+xml" => ".wspolicy", 
			"application/vnd.webturbo" => ".wtb", 
			"video/x-ms-wvx" => ".wvx", 
			"application/x-authorware-bin" => ".x32", 
			"application/vnd.hzn-3d-crossword" => ".x3d", 
			"application/x-silverlight-app" => ".xap", 
			"application/vnd.xara" => ".xar", 
			"application/x-ms-xbap" => ".xbap", 
			"application/vnd.fujixerox.docuworks.binder" => ".xbd", 
			"image/x-xbitmap" => ".xbm", 
			"application/vnd.syncml.dm+xml" => ".xdm", 
			"application/vnd.adobe.xdp+xml" => ".xdp", 
			"application/vnd.fujixerox.docuworks" => ".xdw", 
			"application/xenc+xml" => ".xenc", 
			"application/patch-ops-error+xml" => ".xer", 
			"application/vnd.adobe.xfdf" => ".xfdf", 
			"application/vnd.xfdl" => ".xfdl", 
			"application/xhtml+xml" => ".xht", 
			"application/xhtml+xml" => ".xhtml", 
			"application/xv+xml" => ".xhvml", 
			"image/vnd.xiff" => ".xif", 
			"application/vnd.ms-excel" => ".xla", 
			"application/vnd.ms-excel.addin.macroenabled.12" => ".xlam", 
			"application/vnd.ms-excel" => "xlb.", 
			"application/vnd.ms-excel" => ".xlc", 
			"application/vnd.ms-excel" => ".xlm", 
			"application/vnd.ms-excel" => ".xls", 
			"application/vnd.ms-excel.sheet.binary.macroenabled.12" => ".xlsb", 
			"application/vnd.ms-excel.sheet.macroenabled.12" => ".xlsm", 
			"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => ".xlsx", 
			"application/vnd.ms-excel" => ".xlt", 
			"application/vnd.ms-excel.template.macroenabled.12" => ".xltm", 
			"application/vnd.openxmlformats-officedocument.spreadsheetml.template" => ".xltx", 
			"application/vnd.ms-excel" => ".xlw", 
			"application/xml" => ".xml", 
			"application/vnd.olpc-sugar" => ".xo", 
			"application/xop+xml" => ".xop", 
			"application/xml" => ".xpdl", 
			"application/x-xpinstall" => ".xpi", 
			"image/x-xpixmap" => ".xpm", 
			"application/vnd.is-xpr" => ".xpr", 
			"application/vnd.ms-xpsdocument" => ".xps", 
			"application/vnd.intercon.formnet" => ".xpw", 
			"application/vnd.intercon.formnet" => ".xpx", 
			"application/xml" => ".xsl", 
			"application/xslt+xml" => ".xslt", 
			"application/vnd.syncml+xml" => ".xsm", 
			"application/xspf+xml" => ".xspf", 
			"application/vnd.mozilla.xul+xml" => ".xul", 
			"application/xv+xml" => ".xvm", 
			"application/xv+xml" => ".xvml", 
			"image/x-xwindowdump" => ".xwd", 
			"chemical/x-xyz" => ".xyz", 
			"application/vnd.zzazz.deck+xml" => ".zaz", 
			"application/zip" => ".zip", 
			"application/vnd.zul" => ".zir", 
			"application/vnd.zul" => ".zirz", 
			"application/vnd.handheld-entertainment+xml" => ".zmm" 	
		];

		if($mime){
			return $data[$mime];
		}else{
			return $data;
		}
	}

	/**
	 * 5.3. Iconos vinculados a archivos.
	 */
	public static function fileIcons($file = false){
		$data = [
			'doc' => 'file-word',
			'docx' => 'file-word',
			'gif' => 'image',
			'jpeg' => 'image',
			'jpg' => 'image',
			'ods' => 'file-excel',
			'pdf' => 'file-pdf',
			'png' => 'image',
			'txt' => 'file',
			'xls' => 'file-excel',
			'xlsx' => 'file-excel'
		];

		if($file){
			$icon = false;
			foreach($data as $i => $r){
				if($i == $file){
					$icon = $r;
					break;
				}
			}

			if($icon){
				return $icon;
			}else{
				return 'file';
			}
		}else{
			return $data;	
		}
	}

	/**
	 * 6.1. Porcentaje.
	 */
	public static function percent($qty, $percent, $decimals=2){
		$num = ($qty * $percent)/100;
		$data = round($num, $decimals);
		return (float)$data;
	}

	/**
	 * 6.2. Importe de línea de presupuesto-pedido.
	 *
	 * Para líneas con importe fijo, NO calculado porcentualmente.
	 */
	public static function itemAmount($item, $with_iva = false, $with_equivalence_surcharge = false){
		$amount = round($item->price * $item->qty, 2);

		//Guardamos el precio original antes de descuento:
		$amount_without_discount = $amount;

		if($item->price > 0){
			//Contabilizando jornadas:
	        if($item->days > 1){
	            $amount = $amount * $item->days;  
	            $amount_without_discount = $amount_without_discount * $item->days;  
	            
	            //Aplicando descuento de días continuos:
	            if($item->days_continue > 0 && $item->days_continue_discount){
	                $jc_percentage = helpers::percent($amount, $item->days_continue_discount);
	                $amount = $amount - $jc_percentage;
	            }
	        }

	        //Descuento sobre la línea, si el precio no es devolución:
	        if($item->discount && $item->price > 0){
	            $discount = helpers::percent($amount, $item->discount);   
	            $amount = $amount - $discount;  
	        }
	    }

	    //Guardamos el importe sin IVA (base imponible) para posteriores operaciones:
	    $tax_base = $amount;

	    //Descontando IRPF:
        //29/09/23: se modifica la fórmula a base - irpf
        //14/12/23: nueva modificación de la fórmula: base - irpf, y sobre la base se obtiene también en el IVA, no sobre este resultado.
        $irpf_amount = 0;
        if($item->irpf > 0){
            $irpf_amount = helpers::percent($tax_base, $item->irpf);
        }

        //IVA:	
        $iva_amount = 0;
        if($item->iva > 0 && $with_iva){
        	$iva_amount = helpers::percent($tax_base, $item->iva);
        }

        //Importe sin iva:
        $amount_without_iva = $amount - $irpf_amount;

        //Importe principal: puede llevar IVA o no según se indique en el parámetro $with_iva.
        $amount = $amount + $iva_amount - $irpf_amount;

        //Recargo de equivalencia. Se obtiene sobre la base imponible y se suma al importe total añadido ya el IVA u otros impuestos que pueda haber.
        if($with_equivalence_surcharge && $with_iva && $item->equivalence_surcharge > 0){
        	$percent = ($tax_base * $item->equivalence_surcharge) / 100;	
        	$amount = round($amount + $percent, 2);
        }

        $arr = ['amount' => round($amount,2), 'amount_without_discount' => round($amount_without_discount,2), 'amount_without_iva' => round($amount_without_iva,2), 'iva_amount' => $iva_amount];
        return $arr;
	}

	/**
	 * 6.3. Cálculo importe de línea porcentual.
	 */
	public static function itemAmountPercentage($percentage, $amount){
		if(is_numeric($percentage) && is_numeric($amount)){
			$num = ($percentage * $amount);
			return round($num, 2);
		}else{
			return '';
		}
	}

	/**
	 * 7.1. Render galería imágenes.
	 */
	public static function renderImg($doc, $schema, $gallery = false){
	    $render = '<div class="gallery-item col-sm-3 mb-4">
	        <div class="gallery-img">';
	        if($gallery){
	            $render .= '<a href="/img/'.$schema.'/'.$doc->doc.'" data-gallery="">
	                <img src="/img/'.$schema.'/'.$doc->doc.'">
	                </a>';
	        }else{
	            $render .= '<span class="icon">
	                <i class="fa fa-file mb-2"></i><br>
	                '.$doc->doc.'
	            </span>';    
	        }

	        $render .= '</div><div class="gallery-controls">';
	            //Descargar:
	            if(!$gallery){
	                $render .= '<span>
	                    <a href="/img/'.$schema.'/'.$doc->doc.'" download="'.$doc->doc.'" rel="tooltip" title="'.trans('textos.archivo_descargar').'">
	                        <i class="fa fa-download"></i>
	                    </a>
	                </span>';
	            }

	            //Eliminar:
	            $render .= '<span>
	                <a href="/admin/'.$schema.'-docs/'.$doc->id.'/destroy" class="btn-confirm btn-controls" data-msg="'.ucfirst(trans('textos.archivo_confirm_eliminar')).'" rel="tooltip" title="'.ucfirst(trans('textos.archivo_eliminar')).'">
	                    <i class="fa fa-trash"></i>
	                </a>
	            </span> 
	        </div>
	    </div>';   

	    return $render; 
	}

	/**
	 * 7.2. Render documentos proyectos.
	 */
	public static function renderDocsProjects($doc, $gallery = false){
		$featured = $doc->featured == 1? 'destacado':'';
        $thumbnail = $doc->thumbnail == 1? 'destacado':'';

        $render = '<div class="gallery-item col-sm-3 mb-4">
            <div class="gallery-img">';
            if($gallery){
                $render .= '<a href="/img/projects/'.$doc->doc.'" data-gallery="">
                    <img src="/img/projects/'.$doc->doc.'">
                    </a>';
            }else{
                $render .= '<span class="icon">
                    <i class="fa fa-file mb-2"></i><br>
                    '.$doc->doc.'
                </span>';    
            }

            $render .= '</div><div class="gallery-controls">';
                if($gallery){
                    //Imagen destacada:
                    $render .= '<span>
                        <button class="btn-controls btnDestacar '.$featured.'" rel="tooltip" title="'.ucfirst(trans('textos.imagen_destacada')).'" data-img="'.$doc->id.'" data-project="'.$doc->project_id.'" data-modulo="project-docs">
                            <i class="fa fa-star"></i>  
                        </button>
                    </span>'; 
                  
                    //Thumbnail:
                    $render .= '<span>
                        <button class="btn-controls btnThumb '.$thumbnail.'" rel="tooltip" title="'.ucfirst(trans('textos.imagen_thumbnail')).'" data-img="'.$doc->id.'" data-project="'.$doc->project_id.'" data-modulo="project-docs">
                            <i class="fa fa-image"></i>  
                        </button>
                    </span>';                
                }                

                //Descargar:
                if(!$gallery){
                    $render .= '<span>
                        <a href="/img/projects/'.$doc->doc.'" download="'.$doc->doc.'" rel="tooltip" title="'.trans('textos.archivo_descargar').'">
                            <i class="fa fa-download"></i>
                        </a>
                    </span>';
                }

                //Eliminar:
                $render .= '<span>
                    <a href="/admin/project-docs/'.$doc->id.'/destroy" class="btn-confirm btn-controls" data-msg="'.ucfirst(trans('textos.archivo_confirm_eliminar')).'" rel="tooltip" title="'.ucfirst(trans('textos.archivo_eliminar')).'">
                        <i class="fa fa-trash"></i>
                    </a>
                </span> 
            </div>
        </div>';   

        return $render; 
	}

	/**
	 * 7.3. Render documentos productos.
	 */
	public static function renderDocsProducts($doc, $gallery = false){
		$featured = $doc->featured == 1? 'destacado':'';

        $render = '<div class="gallery-item col-sm-3 mb-4">
            <div class="gallery-img">';
            if($gallery){
                $render .= '<a href="/img/products/'.$doc->doc.'" data-gallery="">
                    <img src="/img/products/'.$doc->doc.'">
                    </a>';
            }else{
                $render .= '<span class="icon">
                    <i class="fa fa-file mb-2"></i><br>
                    '.$doc->doc.'
                </span>';    
            }

            $render .= '</div><div class="gallery-controls">';
                //Imagen destacada:
                if($gallery){
                    $render .= '<span>
                        <button class="btn-controls btnDestacar '.$featured.'" rel="tooltip" title="'.ucfirst(trans('textos.imagen_destacada')).'" data-img="'.$doc->id.'" data-product="'.$doc->product_id.'" data-modulo="product-docs">
                            <i class="fa fa-star"></i>  
                        </button>
                    </span>'; 
                }  

                //Descargar:
                if(!$gallery){
                    $render .= '<span>
                        <a href="/img/products/'.$doc->doc.'" download="'.$doc->doc.'" rel="tooltip" title="'.trans('textos.archivo_descargar').'">
                            <i class="fa fa-download"></i>
                        </a>
                    </span>';
                }

                //Eliminar:
                $render .= '<span>
                    <a href="/admin/product-docs/'.$doc->id.'/destroy" class="btn-confirm btn-controls" data-msg="'.ucfirst(trans('textos.archivo_confirm_eliminar')).'" rel="tooltip" title="'.ucfirst(trans('textos.archivo_eliminar')).'">
                        <i class="fa fa-trash"></i>
                    </a>
                </span> 
            </div>
        </div>';   

        return $render; 	
	}

	/**
	 * 7.4. Scroll to top.
	 */
	public static function scrollToTop(){
		$html = '<a href="#" class="scroll-to-top"><i class="fa fa-chevron-up"></i><span class="sr-only">Go to top</span></a>';
		return $html;
	} 
}