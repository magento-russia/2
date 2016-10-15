<?php
class Df_Dataflow_Model_Registry_Collection_Categories extends Df_Dataflow_Model_Registry_Collection {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Category_Collection
	 */
	protected function createCollection() {
		/** @var Df_Catalog_Model_Resource_Category_Collection $result */
		$result = Df_Catalog_Model_Category::c();
		$result->setStore($this->store());
		/**
		 * Раньше тут стояло
		 * $result->addAttributeToSelect(Df_1C_Const::ENTITY_EXTERNAL_ID);
		 * потому что реестр использовался только модулем 1С:Управление торговлей.
		 *
		 * Теперь же реестр начинает использоваться прикладным решением «Lamoda»,
		 * которому требуется загружать в коллекцию товарных разделов
		 * свойство @see Df_Catalog_Model_Category::P__EXTERNAL_URL
		 *
		 * 2014-09-18
		 * Отныне реестр используется также модулем «Русификация» для перевода демо-данных
		 * @used-by Df_Localization_Onetime_Type_Category::getAllEntities()
		 * А там вообще нам нужны все товарные свойства (чтобы переводить их).
		 */
		$result->addAttributeToSelect('*');
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Catalog_Model_Category::class;}

	/**
	 * @override
	 * @return Df_Core_Model_StoreM
	 */
	protected function getStoreDefault() {return df()->registry()->getStoreProcessed();}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Category $entity
	 * @return void
	 */
	protected function saveEntity(Mage_Core_Model_Abstract $entity) {
		parent::saveEntity($entity);
	}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Category $entity
	 * @param Df_Core_Model_StoreM $store
	 * @return void
	 */
	protected function setStoreToEntity(Mage_Core_Model_Abstract $entity, Df_Core_Model_StoreM $store) {
		$entity->setStoreId($store->getId());
	}

	/**
	 * @param Df_Core_Model_StoreM|null $store [optional]
	 * @return void
	 */
	public static function reset($store = null) {
		if (!$store) {
			$store = df()->registry()->getStoreProcessed();
		}
		if (isset(self::$_s[$store->getId()])) {
			unset(self::$_s[$store->getId()]);
		}
	}

	/**
	 * @param Df_Core_Model_StoreM|null $store [optional]
	 * @return Df_Dataflow_Model_Registry_Collection_Categories
	 */
	public static function s($store = null) {
		if (!$store) {
			$store = df()->registry()->getStoreProcessed();
		}
		if (!isset(self::$_s[$store->getId()])) {
			self::$_s[$store->getId()] = new self(array(self::$P__STORE => $store));
		}
		return self::$_s[$store->getId()];
	}
	/**
	 * Используется методом @see Df_Dataflow_Model_Registry_Collection_Categories::reset()
	 * @var array(int => Df_Dataflow_Model_Registry_Collection_Categories)
	 */
	private static $_s = array();
}