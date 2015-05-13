<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Model_Config_Source_RemoveIfEmpty extends Df_Admin_Model_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return
			array(
				array(
					self::OPTION_KEY__LABEL => 'не удалять'
					,self::OPTION_KEY__VALUE => self::VALUE__NO_REMOVE
				)
				,array(
					self::OPTION_KEY__LABEL => 'удалить'
					,self::OPTION_KEY__VALUE => self::VALUE__REMOVE
				)
				,array(
					self::OPTION_KEY__LABEL => 'удалить, если пуст'
					,self::OPTION_KEY__VALUE => self::VALUE__REMOVE_IF_EMPTY
				)
			)
		;
	}
	const _CLASS = __CLASS__;
	/**
	 * Обратите внимание, что значения должны быть именно строками,
	 * потому что иначе будет сбой метода @see Df_Tweaks_Model_Settings_Remove::getConfigValue()
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
	 */
	const VALUE__NO_REMOVE = '0';
	const VALUE__REMOVE = '1';
	const VALUE__REMOVE_IF_EMPTY = 'remove-if-empty';

	/** @return Df_Admin_Model_Config_Source_RemoveIfEmpty */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}