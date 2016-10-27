<?php
namespace Df\Payment\Config;
/**
 * @method \Df\Payment\Method main()
 * @method \Df\Payment\Config\Manager manager()
 */
abstract class Area extends \Df\Checkout\Module\Config\Area {
	/**
	 * @param string $key
	 * @param bool $canBeTest [optional]
	 * @param string $default [optional]
	 * @return string
	 */
	public function const_($key, $canBeTest = true, $default = '') {return
		$this->constManager()->const_($key, $canBeTest, $default)
	;}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 * @override
	 * @param string $key
	 * @param string $default [optional]
	 * @return string
	 */
	public function getVarWithDefaultConst($key, $default = '') {return
		$this->getVar($key, $this->constManager()->getValue($this->getAreaPrefix(), $key, $default));
	}

	/**
	 * @used-by getConst()
	 * @used-by \Df\Assist\Config\Area\Service::getUrl()
	 * @used-by Df_IPay_Config_Area_Service
	 * @used-by \Df\Payment\Config\Area\Service
	 * @used-by Df_PayOnline_Config_Area_Service
	 * @return \Df\Payment\Config\Manager\ConstT
	 */
	protected function constManager() {return $this->main()->constManager();}
}