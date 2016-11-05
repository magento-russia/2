<?php
class Df_Eav_Model_Config extends Mage_Eav_Model_Config {
	/**
	 * @override
	 * @param Mage_Eav_Model_Entity_Type|int|string $entityType
	 * @param Mage_Core_Model_Abstract|null $object
	 * @return string[]
	 */
	public function getEntityAttributeCodes($entityType, $object = null) {
		$entityType = $this->getEntityType($entityType);
		/** @var int $attributeSetId */
		$attributeSetId = 0;
		if ($object instanceof Varien_Object) {
			$attributeSetId = (int)$object->getDataUsingMethod('attribute_set_id');
		}
		/** @var string $cacheKey */
		$cacheKey = implode('-', array($entityType->getId(), $attributeSetId));
		/** @var string[] $result */
		$result = dfa($this->_attributeCodes, $cacheKey);
		if (!$result) {
			/** @var string $cacheKeyRm */
			/** @var string $resultFromCache */
			$cacheKeyRm = Df_Eav_Model_Cache::s()->makeKey(__METHOD__, $cacheKey);
			$resultFromCache = df_ftn(Df_Eav_Model_Cache::s()->loadDataArray($cacheKeyRm));
			if (
					$resultFromCache
					/**
					 * Раньше тут стояло еще условие:
					  &&
							(
									(df_eav_id_product() !== intval($entityType->getId()))
								||
									!array_diff($resultFromCache, $this->_cachedProductAttributeCodes)
							)
					 *
					 * Однако замеры показали,
					 * что если в оперативном кэше находятся не все свойства товара,
					 * то нам быстрее десериализовывать недостающие поодиночке из постоянного кэша
					 * нежели загрузить все коллекцией из БД.
					 */
			) {
				$result = $resultFromCache;
				$this->_attributeCodes[$cacheKey] = $resultFromCache;
			}
			else {
				$result = parent::getEntityAttributeCodes($entityType, $object);
				if (!$resultFromCache || array_diff($result, $resultFromCache)) {
					Df_Eav_Model_Cache::s()->saveDataArray($cacheKeyRm, $result);
				}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param string $id
	 * @return Varien_Object|null
	 */
	protected function _load($id) {
		/** @var Varien_Object|null $result */
		$result = parent::_load($id);
		/**
		 * Сюда мы попадаем при одновременной установке
		 * Magento CE и Российской сборки Magento,
		 * поэтому надо инициализировать Российскую сборку Magento.
		 */
		Df_Core_Boot::run();
		if (!$result && $this->needCacheRm($id)) {
			$result = df_ftn(Df_Eav_Model_Cache::s()->loadDataComplex($this->getAttributeCacheKey($id)));
			if ($result) {
				parent::_save($result, $id);
				/** @var string $productCode */
				$productCode = str_replace('ATTRIBUTE/catalog_product/', '', $id);
				if ($id !== $productCode) {
					$this->_cachedProductAttributeCodes[]= $productCode;
				}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param mixed $obj
	 * @param mixed $id
	 * @return Mage_Eav_Model_Config
	 */
	protected function _save($obj, $id) {
		if (Mage::isInstalled() && ($obj instanceof Mage_Eav_Model_Entity_Attribute)) {
			/** @var Mage_Eav_Model_Entity_Attribute $obj */
			Df_Eav_Model_Translator::s()->translateAttribute($obj);
		}
		parent::_save($obj, $id);
		/**
		 * Сюда мы попадаем при одновременной установке
		 * Magento CE и Российской сборки Magento,
		 * поэтому надо инициализировать Российскую сборку Magento.
		 */
		Df_Core_Boot::run();
		if ($this->needCacheRm($id)) {
			if ($obj instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
				/** @var Mage_Catalog_Model_Resource_Eav_Attribute $obj */
				/**
				 * Обычная сериализация объектов класса @see Mage_Catalog_Model_Resource_Eav_Attribute невозможна,
				 * потому что эти объекты через свойство
				 * @see Mage_Eav_Model_Entity_Attribute_Abstract::$_entity
				 * cодержат свойства типа @see SimplexmlElement
				 * (в частности, через свойство @see Varien_Db_Adapter_Pdo_Mysql::$_queryHook).
				 * Однако мы можем безболезненно обнулить свойство
				 * @see Mage_Eav_Model_Entity_Attribute_Abstract::$_entity
				 * перед сериализацией!
				 * @see Mage_Eav_Model_Entity_Attribute_Abstract::getEntity():
					 if (!$this->_entity) {
						 $this->_entity = $this->getEntityType();
					 }
					 return $this->_entity;
				 */
				$obj->setEntity(null);
			}
			Df_Eav_Model_Cache::s()->saveDataComplex($this->getAttributeCacheKey($id), $obj);
			/** @var string $productCode */
			$productCode = str_replace('ATTRIBUTE/catalog_product/', '', $id);
			if ($id !== $productCode) {
				$this->_cachedProductAttributeCodes[]= $productCode;
			}
		}
		return $this;
	}

	/**
	 * @param string $id
	 * @return string
	 */
	private function getAttributeCacheKey($id) {return Df_Eav_Model_Cache::s()->makeKey(__CLASS__, $id);}

	/**
	 * @param string $id
	 * @return bool
	 */
	private function needCacheRm($id) {return df_starts_with($id, 'ATTRIBUTE');}

	/** @var string[] */
	private $_cachedProductAttributeCodes = [];
}