<?php
abstract class Df_Payment_Model_Config_Area_Abstract extends Df_Payment_Model_Config_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getAreaPrefix();

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 * @override
	 * @param string $key
	 * @param mixed $defaultValue [optional]
	 * @return mixed|null
	 */
	public function getVar($key, $defaultValue = null) {
		df_param_string_not_empty($key, 0);
		if (!isset($this->{__METHOD__}[$key])) {
			$this->{__METHOD__}[$key] = rm_n_set(
				parent::getVar($this->preprocessKey($key), $defaultValue)
			);
		}
		return rm_n_get($this->{__METHOD__}[$key]);
	}

	/**
	 * @param string $key
	 * @param bool $defaultValue[optional]
	 * @return bool
	 */
	public function getVarFlag($key, $defaultValue = false) {
		return rm_bool($this->getVar($key, $defaultValue));
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 * @override
	 * @param string $key
	 * @param string $defaultValue[optional]
	 * @return mixed
	 */
	public function getVarWithDefaultConst($key, $defaultValue = '') {
		df_param_string($key, 0);
		/** @var mixed $result */
		$result =
			$this->getVar(
				$key
				,$this->getConst(
					rm_config_key($this->getAreaPrefix(), $key)
					,$canBeTest = false
					,$defaultValue
				)
			)
		;
		return $result;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKey($key) {return rm_concat_clean('__', $this->getAreaPrefix(), $key);}

	const _CLASS = __CLASS__;
}