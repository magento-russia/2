<?php
namespace Df\Checkout\Module\Config;
use Df\Checkout\Module\Main as Main;
abstract class Area extends \Df\Checkout\Module\Bridge {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getAreaPrefix();

	/**
	 * @used-by \Df\Checkout\Module\Config\Facade::getAreaForStandardKey()
	 * @param string $key
	 * @return bool
	 */
	public function canProcessStandardKey($key) {return isset($this->_standardKeysFlipped[$key]);}

	/**
	 * @used-by getInt()
	 * @used-by getNat()
	 * @used-by getNat0()
	 * @param string $key
	 * @param mixed $default [optional]
	 * @param \Zend_Validate_Interface|\Zend_Filter_Interface|string|null $validator [optional]
	 * @return mixed|null
	 * @throws \Df\Core\Exception
	 */
	public final function getVar($key, $default = null, $validator = null) {
		if (!isset($this->{__METHOD__}[$key])) {
			/** @var mixed|null $result */
			$result = $this->_getVar($key);
			if (!$validator) {
				if (is_null($result)) {
					$result = $default;
				}
			}
			else {
				$validator = \Df\Core\Validator::resolve($validator);
				try {
					/** @var bool $isFilter */
					$isFilter = $validator instanceof \Zend_Filter_Interface;
					// не фильтруем результат, если он равен null и указано $default
					if ($isFilter && !(is_null($result) && !is_null($default))) {
						$result = $validator->filter($result);
					}
					if (is_null($result)) {
						$result = $default;
					}
					// Если мы уже применили фильтр, то не применяем валидатор,
					// потому что фильтр либо привёл результат к допустимому валидатором значению,
					// либо возбудил исключительную ситуацию.
					if (!$isFilter && $validator instanceof \Zend_Validate_Interface) {
						\Df\Core\Validator::check($result, $validator);
					}
				}
				catch (\Exception $e) {
					/** @var \Df\Core\Exception $e */
					$e = \Df\Core\Exception::wrap($e);
					$e->comment(df_print_params(array('Ключ' => $key)));
					throw $e;
				}
			}
			$this->{__METHOD__}[$key] = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__}[$key]);
	}

	/**
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return bool
	 */
	public function getVarFlag($key, $default = false) {return $this->getVar($key, $default, DF_V_BOOL);}

	/**
	 * @used-by getVar()
	 * Перекрывается методом @see \Df\Shipping\Config\Area::_getVar()
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	protected function _getVar($key, $default = null) {
		return $this->manager()->getValue($this->adaptKey($key), $default);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {return df_ccc('__', $this->getAreaPrefix(), $key);}

	/**
	 * @used-by canProcessStandardKey()
	 * Стандартные параметры, которые ядро Magento запрашивает через getConfigData.
	 * Например: «sort_order».
	 * У наших модулей все свойства имеют приставку в соответствии с областью настроек.
	 * Например, «frontend__sort_order».
	 * Посредством метода getStandardKeys область настроек указывает стандартные ключи,
	 * которые она в состоянии обрабатывать.
	 * @return array
	 */
	protected function getStandardKeys() {return array();}

	/**
	 * 2015-04-05
	 * Пока никем не используется.
	 * @param string $key
	 * @param int $default [optional]
	 * @return int
	 */
	protected function int($key, $default = 0) {return $this->getVar($key, $default, DF_V_INT);}

	/** @return Manager */
	protected function manager() {return Manager::s($this->main());}

	/**
	 * 2015-04-05
	 * @used-by Df_InTime_Config_Area_Service::кодСкладаОтправителя()
	 * @param string $key
	 * @return int
	 */
	protected function nat($key) {return $this->getVar($key, null, DF_V_NAT);}

	/**
	 * 2015-04-05
	 * Пока никем не используется.
	 * @param string $key
	 * @param int $default [optional]
	 * @return int
	 */
	protected function nat0($key, $default = 0) {return $this->getVar($key, $default, DF_V_NAT0);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_standardKeysFlipped = array_flip($this->getStandardKeys());
	}

	/**
	 * @used-by _construct()
	 * @used-by canProcessStandardKey()
	 * @used-by \Df\Shipping\Config\Area::translateStandardKey()
	 * @var array(string|int => string)
	 */
	protected $_standardKeysFlipped;

	/**
	 * @used-by \Df\Checkout\Module\Config\Facade::area()
	 * @param Main $main
	 * @param string $area
	 * @return self
	 */
	public static function sa(Main $main, $area) {
		/** @var array(string => self) $cache */
		static $cache;
		/** @var string $key */
		$key = get_class($main) . '::' . $area;
		if (!isset($cache[$key])) {
			/**
			 * 2015-04-05
			 * Классы с настройками конкретных моделей должны иметь имя по шаблону
			 * <Имя модуля>_Config_Area_<Область действия настроек>
			 * Аналогичное соглашение действует и для общего менеджера настроек:
			 * @see \Df\Checkout\Module\Config\Manager::s()
			 */
			$cache[$key] = self::convention($main, 'Config\Area\\' . df_ucfirst($area));
			df_assert($cache[$key] instanceof self);
		}
		return $cache[$key];
	}
}