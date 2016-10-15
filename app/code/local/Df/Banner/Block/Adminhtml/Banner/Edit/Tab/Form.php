<?php
class Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * @override
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('df_banner_form', array('legend' => 'Параметры рекламного щита'));
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
		$fieldset->addField('identifier', 'text',array(
			'label' => 'Внутреннее системное имя'
			,'class' => 'required-entry'
			,'required' => true
			,'name' => 'identifier'
			,'note' => 'Вы потом будете ссылаться на него в макете'
		));
		$fieldset->addField('title', 'text', array(
			'label' => 'Название'
			,'class' => 'required-entry'
			,'required' => true
			,'name' => 'title'
		));
		$fieldset->addField('show_title', 'select', array(
			'label' => 'Показывать название посетителям?'
			,'name' => 'show_title'
			,'values' => Df_Banner_Model_Status::yesNo()
		));
		$fieldset->addField('width', 'text', array(
			'label' => 'Ширина'
			,'class' => 'required-entry'
			,'required' => true
			,'name' => 'width'
			,'note' => 'в пикселях'
		));
		$fieldset->addField('height', 'text', array(
			'label' => 'Высота'
			,'class' => 'required-entry'
			,'required' => true
			,'name' => 'height'
			,'note' => 'в пикселях'
		));
		$fieldset->addField('delay', 'text', array(
			'label' => 'Продолжительность показа одного объявления'
			,'class' => 'required-entry'
			,'required'  => true
			,'name' => 'delay','note' => 'в милисекундах'
		));
		$fieldset->addField('status', 'select', array(
			'label' => 'Включен?'
			,'name'	=> 'status'
			,'values' => Df_Banner_Model_Status::yesNo()
		));
		$fieldset->addField('content', 'editor', array(
			'name' => 'content'
			,'label' => 'Дополнительный блок текста'
			,'title' => 'Дополнительный блок текста'
			,'style' => 'width:600px; height:300px;'
			,'wysiwyg' => false
			,'required' => false
		));
		if (df_session()->getDfBannerData()) {
			$form->setValues(df_session()->getDfBannerData());
			df_session()->setDfBannerData(null);
		}
		else if (Mage::registry('df_banner_data')) {
			$form->setValues(Mage::registry('df_banner_data')->getData());
		}
		return parent::_prepareForm();
	}
}