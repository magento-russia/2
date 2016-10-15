<?php
class Df_1C_Cml2_Processor_Product_AddExternalId extends Df_Core_Model {
	/** @return string */
	private function getExternalId() {return $this->cfg(self::$P__EXTERNAL_ID);}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::$P__PRODUCT);}

	/** @return void */
	private function process() {
		/** @var Df_Catalog_Model_Product $product */
		$product = $this->getProduct();
		if (!$product->get1CId()) {
			// Данный товар не был импортирован из 1С:Управление торговлей,
			// а был создан администратором магазина вручную.
			// Назначаем этому товару внешний идентификатор.
			rm_1c_log(
				"У товара %s отсутствует внешний идентификатор.\nНазначаем идентификатор «%s»."
				,$product->getTitle(), $this->getExternalId()
			);
			// Добавляем к прикладному типу товаров
			// свойство для учёта внешнего идентификатора товара в 1С:Управление торговлей
			rm_1c_add_external_id_attribute_to_set($product->getAttributeSet());
			$product->saveAttributes(
				array(Df_1C_Const::ENTITY_EXTERNAL_ID => $this->getExternalId())
				// Единое значение для всех витрин
				,$storeId = null
			);
			$product->set1CId($this->getExternalId());
			/** @var Df_Catalog_Model_Product $testProduct */
			$testProduct = df_product($product->getId());
			if ($this->getExternalId() !== $testProduct->get1CId()) {
				df_error('Не удалось добавить внешний идентификатор к товару %s.', $product->getTitle());
			}
			else {
				rm_1c_log('Товару %s назначен внешний идентификатор.', $product->getTitle());
			}
		}
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__EXTERNAL_ID, DF_V_STRING_NE)
			->_prop(self::$P__PRODUCT, Df_Catalog_Model_Product::class)
		;
	}
	/** @var string */
	private static $P__EXTERNAL_ID = 'external_id';
	/** @var string */
	private static $P__PRODUCT = 'product';
	/**
	 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Product::getExternalId()
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Order_Item::getProduct()
	 * @static
	 * @param Df_Catalog_Model_Product $product
	 * @param string $externalId
	 * @return void
	 */
	public static function p(Df_Catalog_Model_Product $product, $externalId) {
		/** @var Df_1C_Cml2_Processor_Product_AddExternalId $processor */
		$processor = new self(array(self::$P__PRODUCT => $product, self::$P__EXTERNAL_ID => $externalId));
		$processor->process();
	}
}