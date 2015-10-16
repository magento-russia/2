<?php
class Df_Catalog_Model_Product_Option extends Mage_Catalog_Model_Product_Option {
	/** @return Df_Catalog_Model_Product_Option */
	public function deleteWithDependencies() {
		$this->getValueInstance()->deleteValue($this->getId());
		$this->deletePrices($this->getId());
		$this->deleteTitles($this->getId());
		$this->delete();
		return $this;
	}

	/**
	 * 2015-10-16
	 * Перевод заголовков опций простых (не настраиваемых) товаров.
	 * @override
	 * @return string
	 */
	public function getTitle() {
		/** @var string|null $original */
		$original = $this['title'];
		/** @var bool $endsWithSemicolon */
		$endsWithSemicolon = rm_ends_with($original, ':');
		if ($endsWithSemicolon) {
			$original = mb_substr($original, 0, -1);
		}
		/** @var string $result */
		$result = rm_translate($original, array('Df_Eav', 'Mage_Catalog'));
		return !$endsWithSemicolon ? $result : $result . ':';
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Catalog_Model_Resource_Product_Option::mf());
	}

	const _CLASS = __CLASS__;
	const P__TITLE = 'title';

	/** @return Df_Catalog_Model_Resource_Product_Option_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Product_Option
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Catalog_Model_Product_Option
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Catalog_Model_Resource_Product_Option::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Catalog_Model_Product_Option */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}