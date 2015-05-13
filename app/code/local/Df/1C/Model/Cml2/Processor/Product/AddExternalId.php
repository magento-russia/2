<?php
class Df_1C_Model_Cml2_Processor_Product_AddExternalId extends Df_Core_Model_Abstract {
	/** @return Df_1C_Model_Cml2_Processor_Product_AddExternalId */
	public function process() {
		/**
		 * Данный товар не был импортирован из 1С:Управление торговлей,
		 * а был создан администратором магазина вручную.
		 * Назначаем этому товару внешний идентификатор.
		 */
		Mage::log(rm_sprintf(
			'У товара «%s» отсутствует внешний идентификатор'
			."\r\nНазначаем этому товару идентификатор «%s»"
			,$this->getProduct()->getName()
			,$this->getExternalId()
		));
		/**
		 * Добавляем к прикладному типу товаров
		 * свойство для учёта внешнего идентификатора товара в 1С:Управление торговлей
		 */
		df_h()->_1c()->cml2()->attributeSet()
			->addExternalIdToAttributeSet($this->getProduct()->getAttributeSetId())
		;
		rm_1c_log(
			'Добавили к прикладному типу товаров №%d группу свойств «%s»'
			,$this->getProduct()->getAttributeSetId()
			,Df_1C_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
		);
		$this->getProduct()->saveAttributes(
			array(Df_Eav_Const::ENTITY_EXTERNAL_ID => $this->getExternalId())
			// Единое значение для всех витрин
			,$storeId = null
		);
		$this->getProduct()->setData(Df_Eav_Const::ENTITY_EXTERNAL_ID, $this->getExternalId());
		/** @var Df_Catalog_Model_Product $testProduct */
		$testProduct = df_product($this->getProduct()->getId());
		if ($this->getExternalId() !== $testProduct->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID)) {
			df_error(
				'Не удалось добавить внешний идентификатор к товару «%s»'
				,$this->getProduct()->getName()
			);
		}
		else {
			rm_1c_log(
				'Товару «%s» назначен внешний идентификатор «%s»'
				,$this->getProduct()->getName()
				,$this->getProduct()->getData(Df_Eav_Const::ENTITY_EXTERNAL_ID)
			);
		}
		return $this;
	}

	/** @return string */
	private function getExternalId() {return $this->cfg(self::P__EXTERNAL_ID);}

	/** @return Df_Catalog_Model_Product */
	private function getProduct() {return $this->cfg(self::P__PRODUCT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__EXTERNAL_ID, self::V_STRING_NE)
			->_prop(self::P__PRODUCT, Df_Catalog_Model_Product::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__EXTERNAL_ID = 'external_id';
	const P__PRODUCT = 'product';
	/**
	 * @static
	 * @param Df_Catalog_Model_Product $product
	 * @param string $externalId
	 * @return Df_1C_Model_Cml2_Processor_Product_AddExternalId
	 */
	public static function i(Df_Catalog_Model_Product $product, $externalId) {
		return new self(array(self::P__PRODUCT => $product, self::P__EXTERNAL_ID => $externalId));
	}
}