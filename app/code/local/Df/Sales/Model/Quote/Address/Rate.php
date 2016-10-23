<?php
class Df_Sales_Model_Quote_Address_Rate extends Mage_Sales_Model_Quote_Address_Rate {
	/**
	 * @used-by STUB::STUB()
	 * @return Df_Shipping_Rate_Result_Method|null
	 */
	public function terms() {return $this->_terms;}

	/**
	 * 2015-04-06
	 * Цель перекрытия — отображение на экране оформления заказа
	 * дополнительной полезной покупателю информации о доступных покупателю способах доставки
	 * (в частности, отображение планируемой даты доставки).
	 * @override
	 * @see Mage_Sales_Model_Quote_Address_Rate::importShippingRate()
	 * @used-by Mage_Sales_Model_Quote_Address::requestShippingRates()
	 * @param Mage_Shipping_Model_Rate_Result_Abstract $rate
	 * @return Df_Sales_Model_Quote_Address_Rate
	 */
	public function importShippingRate(Mage_Shipping_Model_Rate_Result_Abstract $rate) {
		parent::importShippingRate($rate);
		if ($rate instanceof Df_Shipping_Rate_Result_Method) {
			$this->_terms = $rate;
		}
		return $this;
	}

	/** @var Df_Shipping_Rate_Result_Method|null $rate */
	private $_terms = [];
}

