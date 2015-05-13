<?php
class Df_Localization_Model_Onetime_Dictionary_Config_Entry
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string */
	public function getPath() {return $this->getEntityParam('path');}

	/** @return int|null */
	public function getStoreId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getStoreCode()
				? null
				: Mage::app()->getStore($this->getStoreCode())->getId()
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getStoreCode() {return $this->getEntityParam('store');}

	/** @return string */
	public function getValue() {
		/**
		 * Используем @strval(), что что конструкции
		 * <value/> и <value></value>
		 * интерпретировались как пустая строка.
		 * Для установки в БД значения null используйте <value>null</value>
		 */
		return strval($this->getEntityParam('value'));
	}
	/** @return string|null */
	public function getValueOriginal() {return $this->getEntityParam('original_value');}
	/** @return bool */
	public function isLowLevel() {return $this->isChildExist('low_level');}
	/** @return bool */
	public function needSetAsDefault() {return $this->isChildExist('set_as_default');}
	/**
	 * Если искомая фраза начинается или заканчивается символом процента,
	 * то в запросе SQL будем использовать LIKE вместо точного равенства.
	 * @return bool
	 */
	public function useLikeOperator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					rm_starts_with($this->getValueOriginal(), '%')
				||
					rm_ends_with($this->getValueOriginal(), '%')
			;
		}
		return $this->{__METHOD__};
	}

	/** Используется из @see Df_Localization_Model_Onetime_Dictionary_Config_Entries::getItemClass() */
	const _CLASS = __CLASS__;
}


 