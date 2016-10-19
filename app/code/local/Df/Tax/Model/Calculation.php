<?php
// 2016-10-19
use Df_Accounting_Settings_Vat as VAT;
/**
 * 2016-10-19
 * Перекрываем родительский класс,
 * чтобы в случае, когда администратор указал «нет» значением опции
 * «Российская сборка» → «Учёт» → «НДС» → «Является ли интернет-магазин плательщиком НДС
 * при реализации товаров внутри страны?», система не рассчитывала НДС.
 */
class Df_Tax_Model_Calculation extends Mage_Tax_Model_Calculation {
	/**
	 * 2016-10-19
	 * @used-by Mage_Tax_Model_Sales_Total_Quote_Shipping::collect()
	 * @param Varien_Object $request
	 * @return float
	 */
	public function getRate($request) {
		/** @var $vatEnabled $vatEnabled */
		$vatEnabled = VAT::s()->enabled();
		/**
		 * 2016-10-19
		 * Это значение устанавливается здесь: https://github.com/OpenMage/magento-mirror/blob/1.9.3.0/app/code/core/Mage/Tax/Model/Calculation.php#L461
		 * @see getRateRequest()
		 * @var Mage_Core_Model_Store $store
		 */
		$store = $request['store'];
		/** @var string $storeISO2 */
		$storeISO2 = df_store_iso2($store);
		/**
		 * 2016-10-19
		 * Это значение устанавливается здесь: https://github.com/OpenMage/magento-mirror/blob/1.9.3.0/app/code/core/Mage/Tax/Model/Calculation.php#L458
		 * @see getRateRequest()
		 * @var string $addressISO2
		 */
		$addressISO2 = $request['country_id'];
		/**
		 * 2016-10-19
		 * Смысл этого выражения можно пояснить так:
		 * если магазин не является плательщиком НДС внутрисвоей страны
		 * и продажа происходит внутри страны, то мы не рассчитываем НДС (возвращаем ставку 0).
		 */
		return !$vatEnabled && ($storeISO2 === $addressISO2) ? 0 : parent::getRate($request);
	}
}


