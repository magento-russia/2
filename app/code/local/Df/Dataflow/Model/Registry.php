<?php
/**
 * Реестры нам нужны для ускорения доступа к одним и тем же объектам и коллекциям объектов.
 * Эти реестры должны использоваться всеми модулями Российской сборки Magento.
 */
class Df_Dataflow_Model_Registry extends Df_Core_Model_Abstract {
	/** @return Df_Dataflow_Model_Registry_Collection_Attributes */
	public function attributes() {return Df_Dataflow_Model_Registry_Collection_Attributes::s();}

	/** @return Df_Dataflow_Model_Registry_Collection_AttributeSets */
	public function attributeSets() {return Df_Dataflow_Model_Registry_Collection_AttributeSets::s();}
	
	/** @return Df_Dataflow_Model_Registry_Collection_Categories */
	public function categories() {return Df_Dataflow_Model_Registry_Collection_Categories::s();}

	/** @return Mage_Core_Model_Store */
	public function getStoreProcessed() {
		if (!isset($this->_storeProcessed)) {
			/**
			 * Обратите внимание, что магазин можно установить вручную
			 * методом @see Df_Dataflow_Model_Registry::setStoreProcessed()
			 */
			$this->_storeProcessed = rm_state()->getStoreProcessed();
		}
		return $this->_storeProcessed;
	}

	/** @return Df_Dataflow_Model_Registry_MultiCollection_Categories */
	public function multiCategories() {
		return Df_Dataflow_Model_Registry_MultiCollection_Categories::s();
	}

	/** @return Df_Dataflow_Model_Registry_MultiCollection_Products */
	public function multiProducts() {
		return Df_Dataflow_Model_Registry_MultiCollection_Products::s();
	}

	/** @return Df_Dataflow_Model_Registry_Collection_Products */
	public function products() {return Df_Dataflow_Model_Registry_Collection_Products::s();}

	/** @return void */
	public function resetCategories() {Df_Dataflow_Model_Registry_Collection_Categories::reset();}

	/**
	 * @param Mage_Core_Model_Store $storeProcessed
	 * @return void
	 */
	public function setStoreProcessed(Mage_Core_Model_Store $storeProcessed) {
		$this->_storeProcessed = $storeProcessed;
	}

	/** @var Mage_Core_Model_Store */
	private $_storeProcessed;

	/** @return Df_Dataflow_Model_Registry */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}