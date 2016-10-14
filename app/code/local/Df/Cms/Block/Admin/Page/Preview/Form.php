<?php
class Df_Cms_Block_Admin_Page_Preview_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * Preparing from for revision page
	 * @return Df_Cms_Block_Admin_Page_Preview_Form
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
				'id' => 'preview_form','action' => $this->getUrl('*/*/drop', array('_current' => true)),'method' => 'post'
			));
		$data = $this->getFormData();
		if ($data) {
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $subKey => $subValue) {
						$newKey = $key.$subKey;
						$data[$newKey] = $subValue;
						$form->addField($newKey, Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN, array('name' => $key."[$subKey]"));
					}
					unset($data[$key]);
				} else {
					$form->addField($key, Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN, array('name' => $key));
				}
			}
			$form->setValues($data);
		}

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
}