<?php
class Df_C1_Cml2_Import_Data_Entity_Order_Item extends Df_C1_Cml2_Import_Data_Entity {
	/** @return Df_Catalog_Model_Product */
	public function getProduct() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Product $product */
			$product = df_product();
			// Сначала пробуем загрузить товар по его внешнему идентификатору
			/** @var Df_Catalog_Model_Product $result */
			$result = $product->loadByAttribute(Df_C1_Const::ENTITY_EXTERNAL_ID, $this->getExternalId());
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
					Df_C1_Cml2_Processor_Product_AddExternalId::p($result, $this->getExternalId());
				}
			}
			if (!$result || !$result->getId()) {
				df_error(
					'Товар «%s» из заказа «%s» отсутствует в Magento. '
					."\nВы должны настроить экспорт товаров "
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

	/** @return Df_C1_Cml2_Import_Data_Entity_Order */
	private function getEntityOrder() {return $this->cfg(self::P__ENTITY_ORDER);}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {return $this->getEntityOrder()->getOrder();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ENTITY_ORDER, Df_C1_Cml2_Import_Data_Entity_Order::class);
	}
	/**
	 * @used-by Df_C1_Cml2_Import_Data_Collection_Order_Items::itemClass()
	 * @used-by Df_C1_Cml2_Import_Processor_Order_Item::_construct()
	 */

	/**
	 * @used-by Df_C1_Cml2_Import_Data_Entity_Order_Item_Composite::itemParams()
	 * @used-by Df_C1_Cml2_Import_Data_Entity_Order_Item_Composite::i()
	 */
	const P__ENTITY_ORDER = 'entity_order';
}