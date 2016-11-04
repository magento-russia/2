<?php
namespace Df\C1\Config\Api\Product;
class Description extends \Df\C1\Config\Api\Cml2 {
	/** @return string */
	public function getDefault() {return $this->v('default');}
	/** @return boolean */
	public function preserveInUnique() {return $this->getYesNo('preserve_if_unique');}
	/** @return string */
	public function whichFieldToUpdate() {return $this->v('which_field_to_update');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_1c/product__description/';}
	/** @return \Df\C1\Config\Api\Product\Description */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}