<?php
class Df_1C_Cml2_Export_Entry extends Df_Core_Model {
	/**
	 * @param Zend_Date $date
	 * @return string
	 */
	public function date($date) {
		return df_dts($date, Df_1C_Cml2_Export_DocumentMixin::DATE_FORMAT);
	}

	/**
	 * @param string $name
	 * @param float $value
	 * @param bool $includedInTotals
	 * @return array(string => string)
	 */
	public function discount($name, $value, $includedInTotals) {
		return array(
			'Наименование' => $name
			,'УчтеноВСумме' => df_bts($includedInTotals)
			,'Сумма' => df_number_2f($value)
		);
	}

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
	public function tax($name, $value, $includedInTotals) {
		return $this->discount($name, $value, $includedInTotals);
	}

	/**
	 * @param string $type
	 * @param string $value
	 * @return array(string => string)
	 */
	public function type($type, $value) {return array('Тип' => $type, 'Значение' => $value);}

	/** @return array(string => string|array(string => string)) */
	public function unit() {
		return array(
			\Df\Xml\X::ATTR => array(
				'Код' => '796'
				, 'НаименованиеПолное' => 'Штука'
				, 'МеждународноеСокращение' => 'PCE'
			)
			,\Df\Xml\X::CONTENT => 'шт'
		);
	}

	/** @return Df_1C_Cml2_Export_Entry */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}