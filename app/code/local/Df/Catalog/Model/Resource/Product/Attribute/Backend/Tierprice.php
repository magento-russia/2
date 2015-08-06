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
	 * @link http://stackoverflow.com/a/10178922
	 *
	 * @override
	 * @param Varien_Object $priceObject
	 * @return Df_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
	 * @throws Exception
	 */
	public function savePriceData(Varien_Object $priceObject) {
		try {
			/** @var array(string => mixed) $data */
			$data = $this->_prepareDataForTable($priceObject, $this->getMainTable());
			if (df_a($data, $this->getIdFieldName())) {
				parent::savePriceData($priceObject);
			}
			else {
				/** @var Varien_Db_Adapter_Interface $adapter */
				$adapter = $this->_getReadAdapter();
				/** @var Varien_Db_Select $select */
				$select = $adapter->select();
				$select->from($this->getMainTable(), array($this->getIdFieldName()));
				/** @var string $identificationFields */
				$identificationFields =
					array('entity_id', 'customer_group_id', 'website_id', 'all_groups', 'qty')
				;
				/** @var array(string => mixed) $identificationData */
				$identificationData =
					array_intersect_key(
						$data, array_flip($identificationFields)
					)
				;
				foreach ($identificationData as $key => $value) {
					/** @var string $key */
					/** @var mixed $value */
					$select->where(rm_sprintf('%s = ?', $key), $value);
				}
				/** @var array(array(string => mixed)) $rows */
				$rows = $adapter->fetchAll($select);
				if (0 === count($rows)) {
					$adapter->insert($this->getMainTable(), $data);
				}
				else {
					$adapter->update(
						$this->getMainTable()
						,$data
						,rm_quote_into(
							rm_sprintf('%s = ?', $this->getIdFieldName())
							,df_a(rm_first($rows), $this->getIdFieldName())
						)
					);
				}
			}
		}
		catch (Exception $e) {
			Mage::logException($e);
			throw $e;
		}
		return $this;
	}
}