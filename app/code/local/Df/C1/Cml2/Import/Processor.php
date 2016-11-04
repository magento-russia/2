<?php
namespace Df\C1\Cml2\Import;
abstract class Df_C1_Cml2_Import_Processor extends Df_C1_Cml2 {
	/**
	 * @abstract
	 * @return void
	 */
	abstract public function process();

	/** @return Df_C1_Cml2_Import_Data_Entity */
	protected function getEntity() {return $this->cfg(self::$P__ENTITY);}

	/**
	 * @used-by Df_C1_Cml2_Import_Processor_Product_Type::getProductDataNewOrUpdateBase()
	 * @param string $key
	 * @return mixed
	 */
	protected function getStoreConfig($key) {return $this->store()->getConfig($key);}

	/**
	 * @used-by getStoreConfig()
	 * @used-by storeId()
	 * @used-by Df_C1_Cml2_Import_Processor_Product_Type::getProductDataNewOnly()
	 * @used-by Df_C1_Cml2_Import_Processor_Product_Type::getTierPricesInImporterFormat()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return df_state()->getStoreProcessed();}

	/**
	 * @used-by Df_C1_Cml2_Import_Processor_Category::process()
	 * @used-by Df_C1_Cml2_Import_Processor_Product_Type::getProductDataNewOnly()
	 * @return int
	 */
	protected function storeId() {return $this->store()->getId();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, Df_C1_Cml2_Import_Data_Entity::class);
	}
	/**
	 * @used-by _construct()
	 * @used-by getEntity()
	 * @used-by Df_C1_Cml2_Import_Processor_Category::i()
	 * @used-by Df_C1_Cml2_Import_Processor_Order::_construct()
	 * @used-by Df_C1_Cml2_Import_Processor_Order::i()
	 * @used-by Df_C1_Cml2_Import_Processor_Order_Item::_construct()
	 * @used-by Df_C1_Cml2_Import_Processor_Order_Item::ic()
	 * @used-by Df_C1_Cml2_Import_Processor_Product::_construct()
	 * @used-by Df_C1_Cml2_Import_Processor_Product::ic()
	 * @used-by Df_C1_Cml2_Import_Processor_ReferenceList::_construct()
	 * @used-by Df_C1_Cml2_Import_Processor_ReferenceList::i()
	 * @var string
	 */
	protected static $P__ENTITY = 'entity';
}