<?php
namespace Df\C1\Cml2\Import\Data\Collection;
class Products extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return \Df\C1\Cml2\Import\Data\Entity\Product::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Каталог/Товары/Товар';}

	/**
	 * @used-by \Df\C1\Cml2\State\Import\Collections::getProducts()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return \Df\C1\Cml2\Import\Data\Collection\Products
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}