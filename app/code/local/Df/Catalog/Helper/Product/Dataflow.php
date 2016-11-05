<?php
class Df_Catalog_Helper_Product_Dataflow extends Mage_Catalog_Helper_Data {
	/** @return string[] */
	public function getIgnoredFields() {return $this->getFieldNamesByOption('ignore');}

	/** @return string[] */
	public function getInventoryFields() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = [];
			/** @var Mage_Core_Model_Config_Element[] $inventoryNodes */
			$inventoryNodes = $this->getFieldsConfigContainingOption('inventory');
			foreach ($inventoryNodes as $code => $fieldConfig) {
				/** @var string $code */
				/** @var Mage_Core_Model_Config_Element $fieldConfig */
				df_assert_string_not_empty($code);
				df_assert($fieldConfig instanceof Mage_Core_Model_Config_Element);
				$result[]= $code;
				/**
				 * 2015-02-06
				 * Метод @uses Mage_Core_Model_Config_Element::is() возвращает true,
				 * если данный узел XML $fieldConfig содержит дочерний узел с заданным именем.
				 */
				if ($fieldConfig->is('use_config')) {
					$result[]= 'use_config_'.$code;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Dataflow_Model_Importer_Product::getInventoryFields()
	 * @param string $productType
	 * @return string[]
	 */
	public function getInventoryFieldsByProductType($productType) {
		df_param_string_not_empty($productType, 0);
		if (!isset($this->{__METHOD__}[$productType])) {
			/** @var string[] $result */
			$result = [];
			foreach ($this->getFieldsConfigContainingOption('inventory') as $fieldName => $fieldConfig) {
				/** @var string $fieldName */
				/** @var Mage_Core_Model_Config_Element $fieldConfig */
				/** @var Mage_Core_Model_Config_Element $productTypesNode */
				/**
				 * 2015-02-05
				 * Раньше тут стоял код:
						$productTypesNode = @$fieldConfig->product_type;
						df_assert($productTypesNode instanceof SimpleXMLElement);
				 * Думаю, использовать оператор @ — это дурной тон, не стоит.
				 */
				/**
				 * 2015-02-06
				 * Дочерний тег «product_type» обязательно должен присутствовать
				 * в настройках поля для dataflow, например:
					<max_sale_qty>
						<inventory>1</inventory>
						<to_number>1</to_number>
						<use_config>1</use_config>
						<product_type>
							<simple/>
							<virtual/>
						</product_type>
					</max_sale_qty>
				 * Он указывает, для каких системых типов товаров применимы данные настройки.
				 */
				df_assert(df_xml_exists_child($fieldConfig, 'product_type'));
				$productTypesNode = $fieldConfig->{'product_type'};
				/** @var string[] $productTypes */
				$productTypes = [];
				foreach ($productTypesNode->children() as $productTypeNode) {
					/** @var SimpleXMLElement $productTypeNode */
					/** @var string $productTypeName */
					$productTypeName = $productTypeNode->getName();
					df_assert_string_not_empty($productTypeName);
					$productTypes[]= $productTypeName;
				}
				if (in_array($productType, $productTypes)) {
					$result[]= $fieldName;
					/**
					 * 2015-02-06
					 * Метод @uses Mage_Core_Model_Config_Element::is() возвращает true,
					 * если данный узел XML $fieldConfig содержит дочерний узел с заданным именем.
					 */
					if ($fieldConfig->is('use_config')) {
						$result[]= 'use_config_' . $fieldName;
					}
				}
			}
			$this->{__METHOD__}[$productType] = $result;
		}
		return $this->{__METHOD__}[$productType];
	}

	/** @return string[] */
	public function getNumericFields() {return $this->getFieldNamesByOption('to_number');}

	/** @return string[] */
	public function getRequiredFields() {return $this->getFieldNamesByOption('required');}

	/**
	 * @param string $tag
	 * @return string[]
	 */
	private function getFieldNamesByOption($tag) {
		df_assert_string($tag, 0);
		if (!isset($this->{__METHOD__}[$tag])) {
			$this->{__METHOD__}[$tag] = array_keys($this->getFieldsConfigContainingOption($tag));
		}
		return $this->{__METHOD__}[$tag];
	}

	/**
	 * 2015-02-06
	 * @used-by getInventoryFieldsByProductType()
	 * @used-by getFieldNamesByOption()
	 * Этот метод по заданному имени опции $optionName
	 * возвращает ассоциативный массив,
	 * ключами которого являются имена для dataflow всех свойств товара,
	 * настройки для dataflow которых содержат опцию $optionName,
	 * а значениями: сами настройки для dataflow указанных свойтв товара.
	 *
	 * Например, для опции «product_type» метод вернёт массив типа:
	 	array(
	 		'max_sale_qty' =>
				<max_sale_qty>
					<inventory>1</inventory>
					<to_number>1</to_number>
					<use_config>1</use_config>
					<product_type>
						<simple/>
						<virtual/>
					</product_type>
				</max_sale_qty>
	 		,'is_in_stock' =>
				<is_in_stock>
					<inventory>1</inventory>
					<inventory_other>1</inventory_other>
					<product_type>
						<simple/>
						<virtual/>
						<configurable/>
						<grouped/>
					</product_type>
				</is_in_stock>
	 		,(...)
	 	)
	 * То есть, вернёт настройки всех полей,
	 * которые содержат внутри себя дочерний тег «product_type».
	 *
	 * @param string $optionName
	 * @return array(string => Mage_Core_Model_Config_Element)
	 */
	private function getFieldsConfigContainingOption($optionName) {
		df_assert_string_not_empty($optionName, 0);
		if (!isset($this->{__METHOD__}[$optionName])) {
			/** @var array(string => Mage_Core_Model_Config_Element) $result */
			$result = [];
			foreach ($this->getFieldset() as $fieldName => $fieldConfig) {
				/**
				 * 2015-02-06
				 * @var string $fieldName
				 * Смотрите комментарий к методу @uses getFieldset()
				 * Примеры $fieldName: «qty», «max_sale_qty», «is_in_stock» ...
				 */
				/**
				 * 2015-02-06
				 * @var Mage_Core_Model_Config_Element $fieldConfig
				 * Смотрите комментарий к методу @uses getFieldset()
				 * Примеры $fieldConfig:
				 	1)
						<max_sale_qty>
							<inventory>1</inventory>
							<to_number>1</to_number>
							<use_config>1</use_config>
							<product_type>
								<simple/>
								<virtual/>
							</product_type>
						</max_sale_qty>
				 	2)
						<is_in_stock>
							<inventory>1</inventory>
							<inventory_other>1</inventory_other>
							<product_type>
								<simple/>
								<virtual/>
								<configurable/>
								<grouped/>
							</product_type>
						</is_in_stock>
				 */
				/**
				 * 2015-02-06
				 * Метод @uses Mage_Core_Model_Config_Element::is() возвращает true,
				 * если данный узел XML $fieldConfig содержит дочерний узел с заданным именем.
				 */
				if ($fieldConfig->is($optionName)) {
					$result[$fieldName] = $fieldConfig;
				}
			}
			$this->{__METHOD__}[$optionName] = $result;
		}
		return $this->{__METHOD__}[$optionName];
	}

	/**
	 * 2015-02-06
	 * Этот метод возвращает коллекцию всех детей настроечной ветки
	 * «admin/fieldsets/catalog_product_dataflow».
	 * В Magento CE/EE эта настроечная ветка задаётся в 3-х файлах:
	 * 1) Mage/Bundle/etc/config.xml
	 * 2) Mage/Downloadable/etc/config.xml
	 * 3) Mage/Catalog/etc/config.xml
	 * и имеет следующую структуру:
		<admin>
			<fieldsets>
				<catalog_product_dataflow>
					<qty>
						<product_type>
							<downloadable/>
						</product_type>
					</qty>
					<min_qty>
						<product_type>
							<downloadable/>
						</product_type>
					</min_qty>
	 				(...)
				</catalog_product_dataflow>
			(...)
			</fieldsets>
			(...)
		</admin>
	 * Другими словами, метод возвращает объект класса @see Mage_Core_Model_Config_Element,
	 * который является коллекцией других элементов @see Mage_Core_Model_Config_Element,
	 * каждый из которых соответствует узлу внутри ветки «admin/fieldsets/catalog_product_dataflow»,
	 * например:
		1)
			<max_sale_qty>
				<inventory>1</inventory>
				<to_number>1</to_number>
				<use_config>1</use_config>
				<product_type>
					<simple/>
					<virtual/>
				</product_type>
			</max_sale_qty>
		2)
			<is_in_stock>
				<inventory>1</inventory>
				<inventory_other>1</inventory_other>
				<product_type>
					<simple/>
					<virtual/>
					<configurable/>
					<grouped/>
				</product_type>
			</is_in_stock>
	 	3) (...)
	 * @uses Mage_Core_Model_Config::getFieldset()
	 * @return Mage_Core_Model_Config_Element
	 */
	private function getFieldset() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin');
			// Обратите внимание, что значение null мы не должны были получить,
			// потому что работаем с системными параметрами (они всегда должны быть)
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Helper_Product_Dataflow */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}