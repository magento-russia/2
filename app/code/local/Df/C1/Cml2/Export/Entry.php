<?php
namespace Df\C1\Cml2\Export;
use Df\Xml\X as X;
class Entry extends \Df_Core_Model {
	/**
	 * @param \Zend_Date $date
	 * @return string
	 */
	public function date($date) {return df_dts($date, \Df\C1\Cml2\Export\DocumentMixin::DATE_FORMAT);}

	/**
	 * @param string $name
	 * @param float $value
	 * @param bool $includedInTotals
	 * @return array(string => string)
	 */
	public function discount($name, $value, $includedInTotals) {return [
		'Наименование' => $name
		,'УчтеноВСумме' => df_bts($includedInTotals)
		,'Сумма' => df_f2($value)
	];}

	/**
	 * @param string $name
	 * @param string $value
	 * @return array(string => string)
	 */
	public function name($name, $value) {return array('Наименование' => $name, 'Значение' => $value);}

	/**
	 * @param string $name
	 * @param float $value
	 * @param bool $includedInTotals
	 * @return array(string => string)
	 */
	public function tax($name, $value, $includedInTotals) {return
		$this->discount($name, $value, $includedInTotals)
	;}

	/**
	 * @param string $type
	 * @param string $value
	 * @return array(string => string)
	 */
	public function type($type, $value) {return ['Тип' => $type, 'Значение' => $value];}

	/** @return array(string => string|array(string => string)) */
	public function unit() {return [
		X::ATTR => [
			'Код' => '796'
			,'НаименованиеПолное' => 'Штука'
			,'МеждународноеСокращение' => 'PCE'
		]
		,X::CONTENT => 'шт'
	];}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}