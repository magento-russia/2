<?php
class Df_Banner_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	/**
	 * @override
	 * @return string|null
	 */
	public function getTemplate() {
		/** @var string|null $result */
		$result =
			/**
			 * В отличие от витрины, шаблоны административной части будут отображаться
			 * даже если модуль отключен (но модуль должен быть лицензирован)
			 */
			!(df_enabled(Df_Core_Feature::BANNER))
			? null
			: parent::getTemplate()
		;
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banner_Edit_Tabs
	 */
	protected function _beforeToHtml() {
		$this
			->addTab(
				'form_section'
				,array(
					'label' => df_h()->banner()->__('Основные')
					,'title' => df_h()->banner()->__('Основные')
					,'content' => df_block_render(new Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form())
				)
			)
		;
		parent::_beforeToHtml();
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('df_banner_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(df_h()->banner()->__('Настройки'));
	}
}