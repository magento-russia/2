<?php
class Df_1C_Model_Cml2_Import_Data_Entity_Order_Item
	extends Df_1C_Model_Cml2_Import_Data_Entity {
	/** @return Df_Catalog_Model_Product */
	public function getProduct() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Product $product */
			$product = df_product();
			// Сначала пробуем загрузить товар по его внешнему идентификатору
			/** @var Df_Catalog_Model_Product $result */
			$result = $product->loadByAttribute(Df_Eav_Const::ENTITY_EXTERNAL_ID, $this->getExternalId());
			if (false === $result) {
				/**
				 * Раз нам не удалось найти товар в Magento по его внешнему идентификатору,
				 * значит:
				 * [*] либо товар был создан изначально в Magento (и тогда мы ищем его по имени)
				 * [*] либо товар был создан в 1С:Управление торговлей,
				 * и этот товар из-за неправильных настроек профиля обмена данными с WEB-сайтом
				 * в 1С:Управление торговлей не экспортируется в Magento,
				 * и при этом товар был добавлен
				 * к ранее экспортированному из Magento в 1С:Управление торговлей заказу
				 * (в этом случае сигнализируем о неверных данных)
				 */
				$result = $product->loadByAttribute(Df_Catalog_Model_Product::P__NAME, $this->getName());
				if ($result && $result->getId()) {
					// Итак, товар нашли по имени:
					// значит, в Magento у этого товара нет внешнего идентификатора.
					// Добавляем.
					Df_1C_Model_Cml2_Processor_Product_AddExternalId::i($result, $this->getExternalId())
						->process()
					;
				}
			}
			if (!$result || !$result->getId()) {
				df_error(
					'Товар «%s» из заказа «%s» отсутствует в Magento. '
					."\r\nВы должны настроить экспорт товаров "
					."из 1С:Управление торговлей в Magento таким образом, "
					."чтобы этот товар экспортировался в Magento."
					,$this->getName()
					,$this->getOrder()->getIncrementId()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Order */
	private function getEntityOrder() {return $this->cfg(self::P__ENTITY_ORDER);}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {return $this->getEntityOrder()->getOrder();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ENTITY_ORDER, Df_1C_Model_Cml2_Import_Data_Entity_Order::_CLASS);
	}
	/** Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_Order_Items::getItemClass() */
	const _CLASS = __CLASS__;
	const P__ENTITY_ORDER = 'entity_order';
}