<?php
abstract class Df_Page_Model_Admin_Notifier_Merge extends Df_Admin_Model_Notifier_Settings_YesNo {
	/** @return string */
	abstract protected function getFileType();

	/**
	 * @override
	 * @see Df_Admin_Model_Notifier::messageTemplate()
	 * @return string
	 */
	protected function messageTemplate() {return
		'[[Объедините файлы {тип файлов}:]] это значительно ускорит Ваш интернет-магазин.'
	;}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getMessageVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(parent::getMessageVariables(), array(
				'{тип файлов}' => $this->getFileType()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlHelp() {return 'http://magento-forum.ru/topic/4462/';}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlSettingsSuffix() {return 'dev';}
}