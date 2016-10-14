<?php
class Df_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice {
	/**
	 * Цель перекрытия —
	 * устранение следующего дефекта Magento CE/EE:
	 * при программном сохранении товара, имеющего особые цены (tier prices),
	 * система ошибочно принимает уже имеющиеся особые цены за новые,
	 * и по этой причине ошибочно использует оператор SQL INSERT вместо UPDATE,
	 * что приводит к сбою:
	 * «Integrity constraint violation: 1062 Duplicate entry».
	 * http://stackoverflow.com/a/10178922
	 * @override
	 * @param Varien_Object $priceObject
	 * @return Df_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
	 * @throws Exception
	 */
	public function savePriceData(Varien_Object $priceObject) {
		try {
			/** @var array(string => mixed) $data */
			$data = $this->_prepareDataForTable($priceObject, $this->getMainTable());
			if (dfa($data, $this->getIdFieldName())) {
				parent::savePriceData($priceObject);
			}
			else {
				/** @var Varien_Db_Adapter_Interface $adapter */
				$adapter = $this->_getReadAdapter();
				/** @var Varien_Db_Select $select */
				$select = $adapter->select();
				$select->from($this->getMainTable(), $this->getIdFieldName());
				/** @var string $identificationFields */
				$identificationFields = array(
					'entity_id', 'customer_group_id', 'website_id', 'all_groups', 'qty'
				);
				foreach (dfa_select($data, $identificationFields) as $key => $value) {
					/** @var string $key */
					/** @var mixed $value */
					$select->where("{$value} = ?", $key);
				}
				/** @var array(array(string => mixed)) $rows */
				$rows = $adapter->fetchAll($select);
				if (!$rows) {
					$adapter->insert($this->getMainTable(), $data);
				}
				else {
					$adapter->update($this->getMainTable(), $data, rm_quote_into(
						sprintf('%s = ?', $this->getIdFieldName())
						,dfa(df_first($rows), $this->getIdFieldName())
					));
				}
			}
		}
		catch (Exception $e) {
			Mage::logException($e);
			df_error($e);
		}
		return $this;
	}

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
	 */
	public static function s() {
		return Mage::getResourceSingleton('catalog/product_attribute_backend_tierprice');
	}
}