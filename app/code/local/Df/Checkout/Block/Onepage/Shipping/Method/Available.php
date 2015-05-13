<?php
class Df_Checkout_Block_Onepage_Shipping_Method_Available
	extends Mage_Checkout_Block_Onepage_Shipping_Method_Available {
	/**
	 * 2015-03-03
	 * Цель перекрытия —
	 * ускорение реакции системы на экране оформления заказа.
	 * Стандартный блок ошибочно дважды персчитывает одни и те же тарифы доставки,
	 * что замедляет реакцию системы.
	 *
	 * Пока не связываемся со стандартным для Magento CE / EE экраном оформления заказа
	 * с целью экономии времени на тестировании.
	 * Применяем улучшение только для экрана удобного оформления заказа Российской сборки Magento.
	 * @override
	 * @return array(string => Mage_Sales_Model_Quote_Address_Rate[])
	 */
	public function getShippingRates() {
		return
			isset($this->_rates) || df_is_admin() || !rm_checkout_ergonomic()
			? parent::getShippingRates()
			: $this->_rates = $this->getAddress()->getGroupedAllShippingRates()
		;
	}
}


