<?php
class Df_Banner_Block_Adminhtml_Banneritem_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banneritem_Edit_Tab_Form
	 */
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('df_banner_item_form', array ('legend' => 'Настройки'));
		/** @var array(int => string) $banners */
		$banners =array('' => '-- На каком рекламном щите разместить? --');
		/** @var Df_Banner_Model_Resource_Banner_Collection $collection */
		$collection = Df_Banner_Model_Banner::c();
		foreach ($collection as $banner) {
			/** @var Df_Banner_Model_Banner $banner */
			$banners[$banner->getId()] = $banner->getTitle();
		}
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что @uses Varien_Data_Form_Element_Fieldset::addField()
		 * возвращает не $fieldset, а созданное поле.
		 */
		$fieldset->addField('banner_id', 'select', array(
			'label' => 'Щит'
			,'name' => 'banner_id'
			,'required' => true
			,'values' => $banners
		));
		$fieldset->addField('title', 'text', array(
			'label' => 'Заголовок'
			,'class' => 'required-entry'
			,'required' => true
			,'name' => 'title'
		));
		$fieldset->addField('banner_order', 'text', array(
			'label' => 'Порядок показа'
			,'class' => 'required-entry'
			,'required' => true
			,'name' => 'banner_order'
		));
		$fieldset->addField('image', 'image', array(
			'label' => 'Загрузить рекламную картинку:'
			,'required' => false
			,'name' => 'image'
		));
		$fieldset->addField('image_url', 'text', array(
			'label' => 'Указать веб-адрес картинки (вместо загрузки):'
			,'required' => false
			,'name'=> 'image_url'
		));
		$fieldset->addField('thumb_image','image', array(
			'label' => 'Мини-картинка'
			,'required' => false
			,'name' => 'thumb_image'
		));
		$fieldset->addField('thumb_image_url', 'text', array(
			'label' => 'Веб-адрес мини-картинки (вместо загрузки):'
			,'required' => false
			,'name' => 'thumb_image_url'
		));
		$fieldset->addField('link_url', 'text', array(
			'label' => 'Каков веб-адрес объявления посетителя?'
			,'required' => false
			,'name' => 'link_url'
		));
		$fieldset->addField('status', 'select', array(
			'label' => 'Опубликовано?'
			,'name' => 'status'
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
		if (rm_session()->getDfBannerItemData()) {
			$form->setValues(rm_session()->getDfBannerItemData());
			rm_session()->setDfBannerItemData(null);
		}
		else if (Mage::registry('df_banner_item_data')) {
			$form->setValues(Mage::registry('df_banner_item_data')->getData());
		}
		parent::_prepareForm();
		return $this;
	}
}