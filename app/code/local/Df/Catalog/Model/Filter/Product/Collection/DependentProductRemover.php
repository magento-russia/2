<?php
/**
 * Удаляет из коллекции товары, которые являются составными элементами настраиваемого товара
 */
class Df_Catalog_Model_Filter_Product_Collection_DependentProductRemover
	extends Df_Core_Model
	implements Zend_Filter_Interface {
	/** @return Zend_Filter_Interface */
	public function getRejectingFilter() {
		if (!$this->_rejectingFilter) {
			/** @var Zend_Filter $itemsToRemove */
			$this->_rejectingFilter = new Zend_Filter();
			$this->_rejectingFilter
				/**
				 * Обратите внимание, что метод Zend_Filter::appendFilter()
				 * отсутствует в Zend Framework версии 1.9.6, * которая входит в состав Magento 1.4.0.1
				 */
				->addFilter(Df_Catalog_Model_Filter_Product_Collection_Configurable::i())
				->addFilter(Df_Catalog_Model_Filter_Product_Collection_Configurable_Dependent::i())
				/**
				 * На всякий случай добавляем проверку на невидимость в каталоге.
				 * Если товар-элемент настраиваемого товара виден в каталоге и как независимый товар,
				 * то его мы не удаляем из списка товаров-подарков.
				 */
				->addFilter(
					Df_Catalog_Model_Filter_Product_Collection_Visibility::i(
						array(
							Df_Catalog_Model_Validate_Product_Visibility::VALID_VISIBILITY_STATES =>
								array(
									Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
								)
						)
					)
				)
			;
		}
		return $this->_rejectingFilter;
	}
	/** @var Zend_Filter_Interface */
	private $_rejectingFilter;

	/**
	 * Удаляет из коллекции товары, которые являются составными элементами настраиваемого товара
	 * @param array|Traversable $value
	 * @throws Zend_Filter_Exception If filtering $value is impossible
	 * @return Df_Varien_Data_Collection
	 */
	public function filter($value) {
		/** @var Df_Varien_Data_Collection $result */
		$result =
			Df_Varien_Data_Collection::createFromCollection($value)
				->subtract($this->getRejectingFilter()->filter($value))
			;
		;
		df_assert($result instanceof Df_Varien_Data_Collection);
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Catalog_Model_Filter_Product_Collection_DependentProductRemover */
	public static function i() {return new self;}
}