<?php
abstract class Df_Catalog_Model_Admin_Notifier_Flat extends Df_Admin_Model_Notifier_Settings_YesNo {
	/** @return string */
	abstract protected function getConfigPathSuffix();
	/** @return string */
	abstract protected function getTableTypeInGenitiveCase();

	/**
	 * @override
	 * @return string
	 */
	protected function getConfigPath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 'catalog/frontend/' . $this->getConfigPathSuffix();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageTemplate() {
		return
			'[[Денормализуйте]] таблицы {тип таблиц} {перечисление магазинов}:'
			. ' это значительно ускорит Ваш интернет-магазин.'
		;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getMessageVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(parent::getMessageVariables(), array(
				'{тип таблиц}' => $this->getTableTypeInGenitiveCase()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlHelp() {return 'http://magento-forum.ru/topic/3700/#entry15482';}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlSettingsSuffix() {return 'catalog';}
}