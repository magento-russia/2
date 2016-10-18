<?php
// 2016-10-19
class Df_Accounting_Settings_Vat extends Df_Core_Model_Settings {
	/**
	 * 2016-10-19
	 * «Российская сборка» → «Учёт» → «НДС» → «Является ли интернет-магазин плательщиком НДС при реализации товаров внутри страны?»
	 * @return boolean
	 */
	public function enabled() {return $this->getYesNo(__FUNCTION__);}
	
	/**
	 * 2016-10-19
	 * «Российская сборка» → «Учёт» → «НДС» → «Выделять ли НДС?»
	 * @return boolean
	 */
	public function show() {return $this->getYesNo(__FUNCTION__);}	
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_accounting/vat/';}
	/** @return Df_Accounting_Settings_Vat */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}