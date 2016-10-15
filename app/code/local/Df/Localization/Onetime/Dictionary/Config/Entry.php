<?php
class Df_Localization_Onetime_Dictionary_Config_Entry extends Df_Core_Xml_Parser_Entity {
	/** @return string */
	public function getPath() {return $this->leaf('path');}

	/** @return int|null */
	public function getStoreId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getStoreCode() ? null : df_store($this->getStoreCode())->getId()
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getStoreCode() {return $this->leaf('store');}

	/** @return string */
	public function getValue() {
		/**
		 * Используем @strval(), что что конструкции
		 * <value/> и <value></value>
		 * интерпретировались как пустая строка.
		 * Для установки в БД значения null используйте <value>null</value>
		 */
		return strval($this->leaf('value'));
	}
	/** @return string|null */
	public function getValueOriginal() {return $this->leaf('original_value');}
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
					df_starts_with($this->getValueOriginal(), '%')
				||
					df_ends_with($this->getValueOriginal(), '%')
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Config_Entries::itemClass()
	 * @used-by Df_Localization_Onetime_Processor_Config::_construct()
	 */
	const _C = __CLASS__;
}


 