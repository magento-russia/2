<?php
namespace Df\C1\Cml2\Import\Data\Collection;
use Df\C1\Cml2\Import\Data\Entity\Product;
use Df\Xml\X;
class Products extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Product::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Каталог/Товары/Товар';}

	/**
	 * @used-by \Df\C1\Cml2\State\Import\Collections::getProducts()
	 * @static
	 * @param X $e
	 * @return self
	 */
	public static function i(X $e) {return new self([self::$P__E => $e]);}
}