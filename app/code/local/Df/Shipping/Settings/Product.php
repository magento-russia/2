<?php
namespace Df\Shipping\Settings;
class Product extends \Df_Core_Model_Settings {
	/** @return float */
	public function getDefaultHeight() {return $this->getFloat('default__height');}
	/** @return float */
	public function getDefaultLength() {return $this->getFloat('default__length');}
	/**
	 * @param \Df_Core_Model_StoreM|int|string|bool|null $s [optional]
	 * @return float
	 */
	public function getDefaultWeight($s = null) {return $this->getFloat('default__weight', $s);}
	/** @return float */
	public function getDefaultWidth() {return $this->getFloat('default__width');}
	/** @return string */
	public function getUnitsLength() {return $this->v('units__length');}
	/** @return string */
	public function getUnitsWeight() {return $this->v('units__weight');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_shipping/product/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}