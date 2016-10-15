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
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Option_Collection
	 */
	public function getResourceCollection() {return self::c();}

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
		$endsWithSemicolon = df_ends_with($original, ':');
		if ($endsWithSemicolon) {
			$original = mb_substr($original, 0, -1);
		}
		/** @var string $result */
		$result = df_translate($original, array('Df_Eav', 'Mage_Catalog'));
		return !$endsWithSemicolon ? $result : $result . ':';
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Option
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Catalog_Model_Resource_Product_Option::s();}

	/** @used-by Df_Catalog_Model_Resource_Product_Option_Collection::_construct() */

	const P__TITLE = 'title';

	/** @return Df_Catalog_Model_Resource_Product_Option_Collection */
	public static function c() {return new Df_Catalog_Model_Resource_Product_Option_Collection;}
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
	/** @return Df_Catalog_Model_Product_Option */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}