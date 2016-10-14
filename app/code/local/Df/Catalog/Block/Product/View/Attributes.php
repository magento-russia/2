<?php
class Df_Catalog_Block_Product_View_Attributes extends Mage_Catalog_Block_Product_View_Attributes {
	/**
	 * Цели перекрытия:
	 * 1) предоставить администратору возможность скрывать с витринной товарной карточки
	 *    свойства с незаполненными (пустыми) значениями.
	 * 2) кэширование блока свойств товара на витринной товарной карточке.
	 * Метод возвращает массив следующей структуры:
	 * array($attributeCode => array('label' => string, 'value' => string, 'code' => string))
	 * @override
	 * @param string[] $excludeAttr
	 * @return array(string => array(string => string))
	 */
	public function getAdditionalData(array $excludeAttr = array()) {
		/** @var string $cacheKey */
		$cacheKey = $this->getCacheRm()->makeKey(
			__METHOD__, $this->getProduct()->getId() . implode($excludeAttr)
		);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var array(mixed => mixed) $result */
			$result = $this->getCacheRm()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				/** @var bool $needHideEmptyAttributes */
				static $needHideEmptyAttributes;
				if (is_null($needHideEmptyAttributes)) {
					$needHideEmptyAttributes =
						df_module_enabled(Df_Core_Module::TWEAKS)
						&& df_cfg()->tweaks()->catalog()->product()->view()->needHideEmptyAttributes()
					;
				}
				$result =
					$needHideEmptyAttributes
					? $this->getAdditionalDataWithoutEmptyAttributes($excludeAttr)
					: parent::getAdditionalData($excludeAttr)
				;
				$this->getCacheRm()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__}[$cacheKey] = $result;
		}
		return $this->{__METHOD__}[$cacheKey];
	}

	/**
	 * Этот метод отличается от метода
	 * @see Mage_Catalog_Block_Product_View_Attributes::getAdditionalData()
	 * тем, что он не возвращает товарные свойства с пустыми (незаполненными администратором) значениями.
	 * Таким образом, товарные свойства с пустыми значениями не будут отображены на витрине.
	 * @param string[] $excludeAttr
	 * @return array(string => array(string => string))
	 */
	private function getAdditionalDataWithoutEmptyAttributes(array $excludeAttr = array()) {
		/** @var array $data */
		$data = array();
		/** @var Mage_Catalog_Model_Product $product */
		$product = $this->getProduct();
		/** @var array(string => Mage_Catalog_Model_Resource_Eav_Attribute)|Mage_Catalog_Model_Resource_Eav_Attribute[] $attributes */
		$attributes = $product->getAttributes();
		foreach ($attributes as $attribute) {
			/**
			 * Проверял, класс именно этот.
			 * @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute
			 */
			if (
					$attribute->getIsVisibleOnFront()
				&&
					!in_array($attribute->getAttributeCode(), $excludeAttr)
			) {
				// Обратите внимание, что оба условия важны:
				// свойство может как отсутствовать у товара
				// так и присутствовать со значением null.
				if (
						!$product->hasData($attribute->getAttributeCode())
					||
						(is_null($product->getData($attribute->getAttributeCode())))
				) {
					continue;
				}
				/** @var Mage_Eav_Model_Entity_Attribute_Frontend_Abstract $frontend */
				$frontend = $attribute->getFrontend();
				/** @var mixed $value */
				$value = $frontend->getValue($product);
				if (!$product->hasData($attribute->getAttributeCode())) {
					continue;
				} else if ('' === (string)$value) {
					continue;
				} else if (
						('price' === $attribute->getDataUsingMethod('frontend_input'))
					&&
						is_string($value)
				) {
					$value = rm_store()->convertPrice($value, true);
				}
				if (is_string($value) && $value) {
					$data[$attribute->getAttributeCode()] = array(
						'label' => $attribute->getStoreLabel()
						,'value' => $value
						,'code'  => $attribute->getAttributeCode()
					);
				}
			}
		}
		return $data;
	}

	/** @return Df_Core_Model_Cache */
	private function getCacheRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(Mage_Core_Block_Abstract::CACHE_GROUP);
		}
		return $this->{__METHOD__};
	}
}