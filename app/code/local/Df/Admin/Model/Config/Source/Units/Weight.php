<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_Units_Weight extends Df_Admin_Model_Config_Source {
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
			foreach (df()->units()->weight()->getAll() as $unitId => $unitData) {
				/** @var mixed[] $unit */
				df_assert_array($unitData);
				$result[]=
					array(
						self::OPTION_KEY__LABEL =>
							df_a($unitData, Df_Core_Model_Units_Weight::UNIT__LABEL)
						,self::OPTION_KEY__VALUE => $unitId
					)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/** @return Df_Admin_Model_Config_Source_Units_Weight */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}