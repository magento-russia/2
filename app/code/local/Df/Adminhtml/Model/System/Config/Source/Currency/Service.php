<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Adminhtml_Model_System_Config_Source_Currency_Service extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return $this->getAsOptionArray();
	}
	
	/** @return string[][] */
	private function getAsOptionArray() {
		// Здесь кэшировать результат можно,
		// потому что у класса нет параметров.
		if (!isset($this->{__METHOD__})) {
			/** @var string[][] $result */
			$result = array();
			/** @var array $services */
			$services = Mage::getConfig()->getNode('global/currency/import/services')->asArray();
			df_assert_array($result);
			foreach ($services as $code => $service) {
				/** @var array $service */
				df_assert_array($service);
				/** @var string $code */
				df_assert_string_not_empty($code);
				/** @var string $name */
				$name = df_a($service, 'name');
				df_assert_string_not_empty($name);
				/**
				 * Может быть отрицательным числом
				 * @var int $ordering
				 */
				$ordering = rm_int(df_a($service, 'ordering', 0));
				$result[$ordering]=
					array(self::OPTION_KEY__LABEL => $name, self::OPTION_KEY__VALUE => $code)
				;
			}
			// Вот ради этого мы перекрыли родительский класс
			ksort($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	const _CLASS = __CLASS__;
}