<?php
//$Main->user->PagePrivacy('admin');
//header("Content-Type: text/html; charset=windows-1251");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//$csvData = file_get_contents(ROOT_DIR.'/app/modules/static/actions/direct.csv');
//$lines = explode(PHP_EOL, $csvData);
//$array = array();
//$i=0;
//$max=1;
//$max_hh=1;
//$new=array();
//
//function array2csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
//{
//    $f = fopen('php://memory', 'r+');
//    foreach ($data as $item) {
//        fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
//    }
//    rewind($f);
//    return stream_get_contents($f);
//}
//
//foreach ($lines as $line) {
//	if ($i>2) {
//		 $jj=str_getcsv($line,';');
//		 $jj[10].=$max_hh;
//		 $new[]=$jj;
//		$max++;
//		if ($max>1000) {
//			$max=1;
//			$max_hh++;
//		}
//	}
//	$i++;
//
//}
//
//$dd=array2csv($new,';');
//
//file_put_contents(ROOT_DIR.'/app/modules/static/actions/direct2.csv',$dd);
//
//exit;
//$cities=$Taxi->cities->getCities();
//
//$classes = $Taxi->classes->getClasses();
//
//set_time_limit(0);
//ob_start();
//$fp = fopen("php://output", 'w');
//fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
//function yandex_safe($value) {
//	return  preg_replace('/[^a-zA-Zа-яА-ЯёЁ0-9-+!""\'\']+/u', ' ', $value);
//}
//function prepareDesc($title){
//	$title=trim($title);
//	$title=str_replace('(','',$title);
//	$title=str_replace(')','',$title);
//	return $title;
//}
//$fields=array(
//	'Доп. объявление группы',
//	'Мобильное объявление',
//	'Тип объявления',
//	'Название группы',
//	'Тип кампании',
//	'Название кампании',
//	'Валюта',
//	'Фраза (с минус-словами)',
//	'Заголовок 1',
//	'Заголовок 2',
//	'Текст',
//	'Ссылка',
//	'Отображаемая ссылка',
//	'Регион',
//	'Ставка',
//	'Уточнения'
//);
//fputcsv($fp, $fields,';');
//
//$keywords=array(
//	'такси симферополь',
//	'такси аэропорт',
//	'трансфер симферополь',
//	'трансфер аэропорт'
//);
//
//function replaceTt ($tt) {
//	if ( mb_strtolower( $tt ) == mb_strtolower('Курортное (Ленин. р-н)' ) ){
//		$tt='Курортное (Лен.)';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Приморский (Феодосия)' ) ){
//		$tt='Приморский';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Прибрежное (Саки)' ) ){
//		$tt='Прибрежное';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Понизовка (СКК Мрия)' ) ){
//		$tt='Понизовка';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Песочное (Щелкино)' ) ){
//		$tt='Песочное';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Оползневое (Мрия)' ) ){
//		$tt='Оползневое';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Мысовое (Щелкино)' ) ){
//		$tt='Мысовое';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Керчь (переправа)' ) ){
//		$tt='Паром';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Казантип (Поповка)' ) ){
//		$tt='Казантип';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Золотое (Азовленд)' ) ){
//		$tt='Золотое';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Ж/Д Вокзал Симферополь' ) ){
//		$tt='Ж/Д Вокзал Симф';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Береговое (Феодосия)' ) ){
//		$tt='Береговое (Фео)';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Береговое (Бахчисарай)' ) ){
//		$tt='Береговое (Бах)';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Чонгар (граница)' ) ){
//		$tt='Чонгар';
//	}
//	elseif ( mb_strtolower( $tt ) == mb_strtolower('Курортное (Белогорск)' ) ){
//		$tt='Курортное (Бел)';
//	}
//
//	return $tt;
//}
//
//foreach ($cities as $city_from) {
//	$city_from['city_title']=replaceTt($city_from['city_title']);
//
//	$prices = $Taxi->prices->getPricesFrom( $city_from["city_id"] )[ $city_from["city_id"] ];
//	$lll=1;
//	$ww=1;
//	foreach ($prices as $price) {
//
//
//			$to_url   = $price['url'];
//
//			$min_price = 0;
//		$to_title = '';
//			foreach ( $price['classes'] as $class ) {
//				$cur_price = $class['price_value'];
//				$to_title = $class['to_city_title'];
//				$to_title=replaceTt($to_title);
//				if ( $min_price == 0 or $min_price > $cur_price and $cur_price!=0) {
//					$min_price = $cur_price;
//				}
//			}
//
//			if ( $min_price ) {
//
//				if ( mb_strtolower( $city_from['city_title'] ) == 'аэропорт симферополь' ) {
//					$keywords = array(
//						'такси аэропорт симферополь',
//						'такси аэропорт',
//						'трансфер аэропорт симферополь',
//						'трансфер аэропорт'
//					);
//					$tt='Такси Аэропорт';
//					$dd='Крымское такси из аэропорта. Быстрая подача, автомобили с кондиционером и Wi-Fi.';
//					$jj='Аэропорт';
//				} else {
//					$keywords = array(
//						'такси ' . $city_from['city_title'],
//						'трансфер ' . $city_from['city_title']
//					);
//					$tt='Такси '.$city_from['city_title'];
//					$dd='Крымское такси '.$city_from['city_title'].'. Быстрая подача, автомобили с кондиционером и Wi-Fi.';
//					$jj=prepareDesc( $city_from['city_title'] );
//				}
//
//				foreach ( $keywords as $keyword ) {
//					if ($ww>200) {
//						$ww=0;
//						$lll++;
//					}
//					$ww++;
//
//					$fields = array(
//						'-',
//						'-',
//						'Текстово-графическое',
//						'Taxel ' . $city_from['city_title'].' - '.$to_title.' №'.$lll,
//						'Текстово-графическая кампания',
//						'Taxel',
//						'RUB',
//						$keyword . ' ' . prepareDesc( $to_title ),
//						$tt . ' - ' . $to_title,
//						'Цена от ' . format_money( $min_price ) . ' руб.',
//						$dd,
//						$to_url,
//						mb_substr(str_ireplace( ' ', '-', $jj.'-' . prepareDesc($to_title) ),0, 20),
//						'Россия',
//						30,
//						'Низкие цены||Кондиционер||Вежливые водители||Комфортные авто||Без опозданий||Фиксированные цены'
//					);
//					fputcsv( $fp, $fields, ';' );
//				}
//			}
//	}
//}
//
//fclose($fp);
//header("Content-Type:application/csv");
//header('Content-Disposition: attachment;filename="import_data.csv"');
//header('Cache-Control: max-age=0');
//// If you're serving to IE 9, then the following may be needed
//header('Cache-Control: max-age=1');
//
//// If you're serving to IE over SSL, then the following may be needed
//header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
//header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//header ('Pragma: public'); // HTTP/1.0
//exit;
