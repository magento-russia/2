<?php
namespace Df\Payment\Config\Manager\ConstT;
class ModeSpecific extends \Df\Payment\Config\Manager\ConstT {
	/**
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {return df_cc_path(
		$this->getKeyBase()
		, $this->main()->getRmId()
		, $this->main()->isTestMode() ? 'test' : 'production'
		, $key
	);}

	/**
	 * @param \Df\Payment\Method|\Df\Checkout\Module\Main $method
	 * 2016-10-18
	 * Тип параметра — всегда @see \Df\Payment\Method,
	 * но в сигнатуре вынуждены указать @see \Df\Checkout\Module\Main
	 * для совместимости с унаследованным методом @see \Df\Checkout\Module\Config\Manager::s()
	 * @return \Df\Payment\Config\Manager\ConstT\ModeSpecific
	 */
	public static function s(\Df\Checkout\Module\Main $method) {return self::sc(__CLASS__, $method);}
}