<?php
class Df_C1_Validate_Product extends \Df\Zf\Validate {
	/**
	 * @override
	 * @param Df_Catalog_Model_Product $value
	 * @return bool
	 */
	public function isValid($value) {
		df_assert($value instanceof Df_Catalog_Model_Product);
		return !!$value->get1CId();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageInternal() {return
		'Добавляемому в реестр товару должен быть присвоен внешний идентификатор'
	;}

	/** @return Df_C1_Validate_Product */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}