<?php
class Df_Banner_Block_Adminhtml_Banneritem_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
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
	 * @return Df_Banner_Block_Adminhtml_Banneritem_Edit_Tab_Form
	 */
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset =
			$form->addFieldset(
				'df_banner_item_form'
				,array(
					'legend'=>df_h()->banner()->__('Настройки')
				)
			)
		;
		/** @var string[] $banners */
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
		 * потому что addField() возвращает не $fieldset, а созданное поле.
		 */
		$fieldset
			->addField(
				'banner_id'
				,'select'
				,array(
					'label' => df_h()->banner()->__('Щит')
					,'name' => 'banner_id'
					,'required' => true
					,'values' => $banners
				)
			)
		;
		$fieldset
			->addField(
				'title'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Заголовок')
					,'class' => 'required-entry'
					,'required' => true
					,'name' => 'title'
				)
			)
		;
		$fieldset
			->addField(
				'banner_order'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Порядок показа')
					,'class' => 'required-entry'
					,'required' => true
					,'name' => 'banner_order'
				)
			)
		;
		$fieldset
			->addField(
				'image'
				,'image'
				,array(
					'label' => df_h()->banner()->__('Загрузить рекламную картинку:')
					,'required' => false
					,'name' => 'image'
				)
			)
		;
		$fieldset
			->addField(
				'image_url'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Указать веб-адрес картинки (вместо загрузки):')
					,'required' => false
					,'name'=> 'image_url'
				)
			)
		;
		$fieldset
			->addField(
				'thumb_image'
				,'image'
				,array(
					'label' => df_h()->banner()->__('Мини-картинка')
					,'required' => false
					,'name' => 'thumb_image'
				)
			)
		;
		$fieldset
			->addField(
				'thumb_image_url'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Веб-адрес мини-картинки (вместо загрузки):')
					,'required' => false
					,'name' => 'thumb_image_url'
				)
			)
		;
		$fieldset
			->addField(
				'link_url'
				,'text'
				,array(
					'label' =>
						df_h()->banner()->__(
							'Каков веб-адрес объявления посетителя?'
						)
					,'required' => false
					,'name' => 'link_url'
				)
			)
		;
		$fieldset
			->addField(
				'status'
				,'select'
				,array(
					'label' => df_h()->banner()->__('Опубликовано?')
					,'name' => 'status'
					,'values' =>
						array(
							array(
								'value'	=> 1
								,'label' => df_h()->banner()->__('Да')
							)
							,array(
								'value' => 2
								,'label' => df_h()->banner()->__('Нет')
							)
						)
				)
			)
		;
		$fieldset
			->addField(
				'content'
				,'editor'
				,array(
					'name' => 'content'
					,'label' => df_h()->banner()->__('Дополнительный блок текста')
					,'title' => df_h()->banner()->__('Дополнительный блок текста')
					,'style' => 'width:600px; height:300px;'
					,'wysiwyg' => false
					,'required' => false
				)
			)
		;
		if (df_mage()->adminhtml()->session()->getDfBannerItemData()) {
			$form->setValues(df_mage()->adminhtml()->session()->getDfBannerItemData());
			df_mage()->adminhtml()->session()->setDfBannerItemData(null);
		}
		else if (Mage::registry('df_banner_item_data')) {
			$form->setValues(Mage::registry('df_banner_item_data')->getData());
		}
		parent::_prepareForm();
		return $this;
	}
}