<?php
class Df_Shipping_Model_Config extends Mage_Shipping_Model_Config {
	/**
	 * Цель перекрытия —
	 * улучшение диагностики в тех ситуациях,
	 * когда требуемый класс некоего модуля доставки отсутствует.
	 * @override
	 * @param string $code
	 * @param array(string => mixed) $config
	 * @param Mage_Core_Model_Store|string|int|null $store
	 * @return Mage_Shipping_Model_Carrier_Abstract|bool
	 */
	protected function _getCarrier($code, $config, $store = null) {
		/** @var Mage_Shipping_Model_Carrier_Abstract|bool result */
		$result = false;
		/** @var string|null $modelName */
		$modelName = df_a($config, 'model');
		if (!is_null($modelName)) {
			/**
			* Added protection from not existing models usage.
			* Related with module uninstall process
			*/
			try {
				$result = df_model($modelName);
				df_assert($result instanceof Mage_Shipping_Model_Carrier_Abstract);
				$result->setId($code);
				$result->setDataUsingMethod('store', $store);
				self::$_carriers[$code] = $result;
			}
			catch(Exception $e) {
				Mage::logException($e);
				$result = false;
			}
		}
		return $result;
	}
}