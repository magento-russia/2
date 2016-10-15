<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Adminhtml_Model_System_Config_Source_Currency_Service extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {return $this->getAsOptionArray();}
	
	/** @return string[][] */
	private function getAsOptionArray() {
		// Здесь кэшировать результат можно,
		// потому что у класса нет параметров.
		if (!isset($this->{__METHOD__})) {
			/** @var string[][] $result */
			$result = array();
			foreach (df_config_a('global/currency/import/services') as $code => $service) {
				/** @var array $service */
				df_assert_array($service);
				/** @var string $code */
				df_assert_string_not_empty($code);
				/** @var string $name */
				$name = dfa($service, 'name');
				df_assert_string_not_empty($name);
				/**
				 * Может быть отрицательным числом
				 * @var int $ordering
				 */
				$ordering = df_int(dfa($service, 'ordering', 0));
				$result[$ordering]= df_option($code, $name);
			}
			// Вот ради этого мы перекрыли родительский класс
			ksort($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}