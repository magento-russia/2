<?php
class Df_Banner_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container {
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
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_controller = 'adminhtml_banner';
		$this->_blockGroup = 'df_banner';
		$this->_headerText = df_h()->banner()->__('Рекламные щиты');
		$this->_addButtonLabel = df_h()->banner()->__('Повесить новый щит...');
	}
	const _CLASS = __CLASS__;
}