<?php
namespace Df\Checkout\Module\Config;
use Df\Checkout\Module\Main as Main;
abstract class Manager extends \Df\Checkout\Module\Bridge {
	/**
	 * @param mixed $key
	 * @return mixed
	 */
	abstract protected function _getValue($key);

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getKeyBase();

	/**
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function getValue($key, $default = null) {
		/** @var string $result */
		$result = $this->_getValue($this->adaptKey($key));
		return $this->adaptValue($this->isValueEmpty($result) ? $default : $result);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {return
		df_cc_path($this->getKeyBase(), $this->main()->getRmId(), $key)
	;}

	/**
	 * @param mixed $value
	 * @return mixed
	 * @throws \Exception
	 */
	private function adaptValue($value) {
		/** @var mixed $result */
		$result = $value;
		/** @var bool $doing */
		static $doing = false;
		if (!$doing && is_string($result) && df_contains($result, '{')) {
			// чтобы не попадать в рекурсию
			$doing = true;
			try {
				$result = strtr($result, $this->getTemplates());
			}
			catch (\Exception $e) {
				$doing = false;
				throw $e;
			}
			$doing = false;
		}
		return $result;
	}

	/** @return array(string => string) */
	private function getTemplates() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(
				array('{телефон магазина}' => df_cfg()->base()->getStorePhone($this->store()))
				,$this->main()->getConfigTemplates()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Нельзя называть этот метод isEmpty(),
	 * потому что метод с таким именем уже имеется: @see Varien_Object::isEmpty()
	 * @used-by getValue()
	 * @param mixed $value
	 * @return bool
	 */
	private function isValueEmpty($value) {return is_null($value) || df_empty_string($value);}

	/**
	 * @used-by \Df\Checkout\Module\Config\Area::manager()
	 * @static
	 * @param Main $main
	 * @return self
	 */
	public static function s(Main $main) {
		/** @var array(string => self) */
		static $cache;
		/** @var string $key */
		$key = get_class($main);
		if (!isset($cache[$key])) {
			/**
			 * @var string $class
			 * @see \Df\Payment\Config\Manager
			 * @see \Df\Shipping\Config\Manager
			 */
			/**
			 * 2015-04-05
			 * Менеджер настроек конкретного модуля должен иметь имя по шаблону
			 * <Имя модуля>\Config\Manager
			 * Аналогичное соглашение действует и для классов с настройками конкретных областей:
			 * @see \Df\Checkout\Module\Config\Area::sa()
			 */
			$class = df_cc_class('Df', $main->getCheckoutModuleType(), 'Config\Manager');
			$cache[$key] = self::ic($class, $main);
			df_assert($cache[$key] instanceof self);
		}
		return $cache[$key];
	}

	/**
	 * @used-by \Df\Shipping\Config\Manager::s()
	 * @used-by \Df\Shipping\Config\Manager\Legacy::s()
	 * @used-by \Df\Payment\Config\Manager\ConstT::s()
	 * @used-by \Df\Payment\Config\Manager\ConstT\DefaultT::s()
	 * @used-by \Df\Payment\Config\Manager\ConstT\ModeSpecific::s()
	 * @static
	 * @param string $class
	 * @param Main $main
	 * @return self
	 */
	protected static function sc($class, Main $main) {
		/** @var string $key */
		$key = df_ckey(get_class($main), $class);
		/** @var array(string => self) */
		static $cache;
		if (!isset($cache[$key])) {
			$cache[$key] = self::ic($class, $main);
		}
		return $cache[$key];
	}
}