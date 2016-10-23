<?php
class Df_Checkout_Module_Config_Facade extends Df_Checkout_Module_Bridge {
	/** @return Df_Checkout_Module_Config_Area */
	public function admin() {return $this->area(__FUNCTION__);}

	/** @return Df_Checkout_Module_Config_Area */
	public function frontend() {return $this->area(__FUNCTION__);}

	/**
	 * Этот метод опосредованно вызывается ядром Magento, например:
	 * @see Mage_Shipping_Model_Shipping::collectCarrierRates()
			if ($carrier->getConfigData('shipment_requesttype')) {
	 		(...)
			if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
	 * @used-by Df_Payment_Method::getConfigData()
	 * @used-by Df_Shipping_Carrier::getConfigData()
	 * @override
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function getVar($key, $default = null) {
		return $this->getAreaForStandardKey($key)->getVar($key, $default);
	}

	/** @return Df_Checkout_Module_Config_Area */
	public function service() {return $this->area(__FUNCTION__);}

	/**
	 * @param string $area
	 * @return Df_Checkout_Module_Config_Area
	 */
	private function area($area) {return Df_Checkout_Module_Config_Area::sa($this->main(), $area);}

	/**
	 * @param string $key
	 * @return Df_Checkout_Module_Config_Area
	 */
	private function getAreaForStandardKey($key) {
		df_param_string_not_empty($key, 0);
		if (!isset($this->{__METHOD__}[$key])) {
			/** @var Df_Checkout_Module_Config_Area $result */
			$result = Df_Checkout_Module_Config_Area_No::s($this->main());
			foreach ($this->getAreas() as $area) {
				/** @var Df_Checkout_Module_Config_Area $area */
				if ($area->canProcessStandardKey($key)) {
					$result = $area;
					break;
				}
			}
			$this->{__METHOD__}[$key] = $result;
		}
		return $this->{__METHOD__}[$key];
	}

	/** @return Df_Checkout_Module_Config_Area[] */
	private function getAreas() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array($this->frontend(), $this->admin(), $this->service());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Checkout_Module_Main $main
	 * @return Df_Checkout_Module_Config_Facade
	 */
	public static function s(Df_Checkout_Module_Main $main) {
		/** @var array(string => Df_Checkout_Module_Config_Facade) $cache */
		static $cache;
		/** @var string $key */
		$key = get_class($main);
		if (!isset($cache[$key])) {
			$cache[$key] = self::ic(__CLASS__, $main);
		}
		return $cache[$key];
	}
}