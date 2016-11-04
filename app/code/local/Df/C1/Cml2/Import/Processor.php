<?php
namespace Df\C1\Cml2\Import;
abstract class Processor extends \Df\C1\Cml2 {
	/**
	 * @abstract
	 * @return void
	 */
	abstract public function process();

	/** @return \Df\C1\Cml2\Import\Data\Entity */
	protected function getEntity() {return $this->cfg(self::$P__ENTITY);}

	/**
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getProductDataNewOrUpdateBase()
	 * @param string $key
	 * @return mixed
	 */
	protected function getStoreConfig($key) {return $this->store()->getConfig($key);}

	/**
	 * @used-by getStoreConfig()
	 * @used-by storeId()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getProductDataNewOnly()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getTierPricesInImporterFormat()
	 * @return \Df_Core_Model_StoreM
	 */
	protected function store() {return df_state()->getStoreProcessed();}

	/**
	 * @used-by \Df\C1\Cml2\Import\Processor\Category::process()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getProductDataNewOnly()
	 * @return int
	 */
	protected function storeId() {return $this->store()->getId();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, \Df\C1\Cml2\Import\Data\Entity::class);
	}
	/**
	 * @used-by _construct()
	 * @used-by getEntity()
	 * @used-by \Df\C1\Cml2\Import\Processor\Category::i()
	 * @used-by \Df\C1\Cml2\Import\Processor\Order::_construct()
	 * @used-by \Df\C1\Cml2\Import\Processor\Order::i()
	 * @used-by \Df\C1\Cml2\Import\Processor\Order\Item::_construct()
	 * @used-by \Df\C1\Cml2\Import\Processor\Order\Item::ic()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product::_construct()
	 * @used-by \Df\C1\Cml2\Import\Processor\Product::ic()
	 * @used-by \Df\C1\Cml2\Import\Processor\ReferenceList::_construct()
	 * @used-by \Df\C1\Cml2\Import\Processor\ReferenceList::i()
	 * @var string
	 */
	protected static $P__ENTITY = 'entity';
}