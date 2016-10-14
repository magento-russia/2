<?php
/**
 * @method Df_Localization_Onetime_Dictionary_Rule_Actions_Page getActions()
 * @method Df_Cms_Model_Page getEntity()
 */
class Df_Localization_Onetime_Processor_Cms_Page
	extends Df_Localization_Onetime_Processor_Cms {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {
		return array('content', 'content_heading', 'layout_update_xml'
			,'meta_keywords', 'meta_description', $this->getTitlePropertyName()
		);
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatablePropertiesCustom() {
		return array('content_heading', $this->getTitlePropertyName());
	}

	/**
	 * @override
	 * @return void
	 */
	protected function updateProperties() {
		if ($this->getActions()->getContentNew()) {
			$this->getEntity()->setContent($this->getActions()->getContentNew());
		}
		/**
		 * Обратите внимание, что перевод по словарю мы выполняем
		 * даже при наличии правила new_content,
		 * потому что помимо свойства content
		 * нам, возможно, потребуется перевести и другие свойства:
		 * @see getTranslatableProperties();
		 */
		parent::updateProperties();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, Df_Cms_Model_Page::_C);
	}
}