<?php
/**
 * Обработчик события.
 *
 * Обратите внимание, что «событие» и «обработчик события» — два разных объекта.
 * Это даёт возможность инкапсулировать программный код класса «событие»
 * и повторго использовать этот программный код для разных обработчиков событий
 */
abstract class Df_Core_Model_Handler extends Df_Core_Model_Abstract {
	/**
	 * @static
	 * @param string $class
	 * @param Df_Core_Model_Event $event
	 * @param array $additionalParams[optional]
	 * @return Df_Core_Model_Handler
	 */
	public static function create($class, Df_Core_Model_Event $event, $additionalParams = array()) {
		df_param_string($class, 0);
		df_param_array($additionalParams, 2);
		$result = df_model($class, array_merge(array(self::P__EVENT => $event), $additionalParams));
		df_assert($result instanceof Df_Core_Model_Handler);
		return $result;
	}

	/**
	 * Метод-обработчик события
	 *
	 * @abstract
	 * @return void
	 */
	abstract public function handle();

	/**
	 * Класс события (для валидации события)
	 * @abstract
	 * @return string
	 */
	abstract protected function getEventClass();

	/** @return Df_Core_Model_Event */
	protected function getEvent() {
		return $this->cfg(self::P__EVENT);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__EVENT, $this->getEventClass());
	}
	const _CLASS = __CLASS__;
	const P__EVENT = 'event';
}