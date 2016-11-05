<?php
namespace Df\C1\Cml2\Import\Data\Collection;
use Df\C1\Cml2\Import\Data\Entity\Category;
class Categories extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Category::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return $this->cfg(self::$P__XML_PATH_AS_ARRAY, 'Группы/Группа');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__XML_PATH_AS_ARRAY, DF_V_ARRAY, false);
	}
	/** @var string */
	private static $P__XML_PATH_AS_ARRAY = 'xml_path_as_array';
	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\Category::getChildren()
	 * @used-by \Df\C1\Cml2\State\Import\Collections::getCategories()
	 * @static
	 * @param \Df\Xml\X $xml
	 * @param array|null $pathAsArray [optional]
	 * @return self
	 */
	public static function i(\Df\Xml\X $xml, $pathAsArray = null) {return new self([
		self::$P__E => $xml, self::$P__XML_PATH_AS_ARRAY => $pathAsArray
	]);}
}