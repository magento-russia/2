<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_RemoveIfEmpty extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::NO_REMOVE => 'не удалять'
			,self::REMOVE => 'удалить'
			,self::REMOVE_IF_EMPTY => 'удалить, если пуст'
		));
	}
	/**
	 * Обратите внимание, что значения должны быть именно строками,
	 * потому что иначе будет сбой метода @used-by Df_Tweaks_Model_Settings_Remove::getConfigValue()
	 * при отсутствии в базе данных значения:
			$result = Mage::getStoreConfig($this->translateConfigKeyFromShortToFull($shortKey));
			if (is_null($result)) {
				$result = $defaultValue;
			}
			df_result_string($result);
	 * Обратите внимание, что дефект был допущен 22 апреля 2013 года,
	 * однако у клиентов сбой произошёл только 17 сентября 2013 года.
	 * Это говорит о том, что в эту ветку программного кода мы попадаем редко.
	 *
	 * Вообще, в эту ветку мы можем попадать только в том случае, когда
		Mage::getStoreConfig($this->translateConfigKeyFromShortToFull($shortKey));
	 * возвращает null.
	 *
	 * Однако у нас для всех настроечных ключей есть непустые значения в ветке default.
	 * Странно, что же это за ключ такой, что Mage::getStoreConfig возвращает null?
	 * @used-by Df_Tweaks_Model_Settings_Remove::value()
	 */
	const NO_REMOVE = '0';
	/** @used-by Df_Tweaks_Model_Handler_Remover::getInvisibleStates() */
	const REMOVE = '1';
	/** @used-by Df_Tweaks_Model_Handler_Remover::getInvisibleStates() */
	const REMOVE_IF_EMPTY = 'remove-if-empty';
}