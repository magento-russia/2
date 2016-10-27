<?php
namespace Df\Checkout\Module\Config;
use Df\Checkout\Module\Main as Main;
class Facade extends \Df\Checkout\Module\Bridge {
	/** @return Area */
	public function admin() {return $this->area(__FUNCTION__);}

	/** @return Area */
	public function frontend() {return $this->area(__FUNCTION__);}

	/**
	 * Этот метод опосредованно вызывается ядром Magento, например:
	 * @see Mage_Shipping_Model_Shipping::collectCarrierRates()
			if ($carrier->getConfigData('shipment_requesttype')) {
	 		(...)
			if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
	 * @used-by \Df\Payment\Method::getConfigData()
	 * @used-by \Df\Shipping\Carrier::getConfigData()
	 * @override
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function getVar($key, $default = null) {return
		$this->getAreaForStandardKey($key)->getVar($key, $default)
	;}

	/** @return Area */
	public function service() {return $this->area(__FUNCTION__);}

	/**
	 * @param string $area
	 * @return Area
	 */
	private function area($area) {return Area::sa($this->main(), $area);}

	/**
	 * @param string $key
	 * @return Area
	 */
	private function getAreaForStandardKey($key) {
		df_param_string_not_empty($key, 0);
		if (!isset($this->{__METHOD__}[$key])) {
			/** @var Area $result */
			$result = Area\No::s($this->main());
			foreach ($this->getAreas() as $area) {
				/** @var Area $area */
				if ($area->canProcessStandardKey($key)) {
					$result = $area;
					break;
				}
			}
			$this->{__METHOD__}[$key] = $result;
		}
		return $this->{__METHOD__}[$key];
	}

	/** @return Area[] */
	private function getAreas() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array($this->frontend(), $this->admin(), $this->service());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Main $main
	 * @return self
	 */
	public static function s(Main $main) {
		/** @var array(string => self) $cache */
		static $cache;
		/** @var string $key */
		$key = get_class($main);
		if (!isset($cache[$key])) {
			$cache[$key] = self::ic(__CLASS__, $main);
		}
		return $cache[$key];
	}
}