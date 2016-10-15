<?php
class Df_Directory_Setup_Entity_Region extends Df_Core_Model {
	/** @return string */
	public function getCapital() {return $this->cfg(self::$P__CAPITAL);}
	/** @return string */
	public function getCode() {return $this->cfg(self::$P__CODE);}
	/** @return string */
	public function getName() {return $this->cfg(self::$P__NAME);}
	/** @return int */
	public function getType() {return $this->cfg(self::$P__TYPE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__NAME, DF_V_STRING_NE)
			->_prop(self::$P__CODE, DF_V_STRING_NE)
			->_prop(self::$P__TYPE, DF_V_INT)
			/**
			 * У Московской и Ленинградской областей как бы нет столицы.
			 * @see Df_Directory_Setup_2_0_0::getRussianRegions()
			 * http://magento-forum.ru/topic/4376/
			 */
			->_prop(self::$P__CAPITAL, DF_V_STRING)
		;
	}	

	/** @used-by Df_Directory_Setup_Processor_Region::_construct() */

	/** @var string */
	private static $P__CAPITAL = 'capital';
	/** @var string */
	private static $P__CODE = 'code';
	/** @var string */
	private static $P__NAME = 'name';
	/** @var string */
	private static $P__TYPE = 'type';
	/**
	 * @static
	 * @param string $name
	 * @param string|null $capital
	 * @param string $code
	 * @param int $type
	 * @return Df_Directory_Setup_Entity_Region
	 */
	public static function i($name, $capital, $code, $type) {return new self(array(
		self::$P__NAME => $name
		, self::$P__CAPITAL => $capital
		, self::$P__CODE => $code
		, self::$P__TYPE => $type
	));}
}