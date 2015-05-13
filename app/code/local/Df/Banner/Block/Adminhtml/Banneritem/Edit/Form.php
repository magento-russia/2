<?php
class Df_Banner_Block_Adminhtml_Banneritem_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
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

  protected function _prepareForm()
  {
	  $form = new Varien_Data_Form(array(
									  'id' => 'edit_form',  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),  'method' => 'post',  'enctype' => 'multipart/form-data'
								   )
	  );
	  $form->setUseContainer(true);
	 $this->setForm($form);
	  return parent::_prepareForm();
  }
}