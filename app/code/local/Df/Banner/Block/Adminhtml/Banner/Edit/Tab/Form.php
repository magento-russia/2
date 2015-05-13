<?php
class Df_Banner_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
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
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset =
			$form
				->addFieldset(
					'df_banner_form'
					,array('legend'=>df_h()->banner()->__('Параметры рекламного щита')
				)
			)
		;
		/**
		 * Обратите внимание,
		 * что нельзя применять цепной вызов $fieldset->addField()->addField(),
		 * потому что addField() возвращает не $fieldset, а созданное поле.
		 */
		$fieldset
			->addField(
				'identifier'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Внутреннее системное имя')
					,'class' => 'required-entry'
					,'required' => true
					,'name' => 'identifier'
					,'note' => 'Вы потом будете ссылаться на него в макете'
				)
			)
		;
		$fieldset
			->addField(
				'title'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Название')
					,'class' => 'required-entry'
					,'required' => true
					,'name' => 'title'
				)
			)
		;
		$fieldset
			->addField(
				'show_title'
				,'select'
				,array(
					'label' => df_h()->banner()->__('Показывать название посетителям?')
					,'name' => 'show_title'
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
				'width'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Ширина')
					,'class' => 'required-entry'
					,'required' => true
					,'name' => 'width'
					,'note' => 'в пикселях'
				)
			)
		;
		$fieldset
			->addField(
				'height'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Высота')
					,'class' => 'required-entry'
					,'required' => true
					,'name' => 'height'
					,'note' => 'в пикселях'
				)
			)
		;
		$fieldset
			->addField(
				'delay'
				,'text'
				,array(
					'label' => df_h()->banner()->__('Продолжительность показа одного объявления')
					,'class' => 'required-entry'
					,'required'  => true
					,'name' => 'delay','note' => 'в милисекундах'
				)
			)
		;
	/*
	  $fieldset->addField('active_from', 'text', array(
		  'label'	 => df_h()->banner()->__('Active From'),  'required'  => false,  'name'	  => 'active_from',  ));
	  $fieldset->addField('active_to', 'text', array(
		  'label'	 => df_h()->banner()->__('Active To'),  'required'  => false,  'name'	  => 'active_to',  ));
	 */

		$fieldset
			->addField(
				'status'
				,'select'
				,array(
					'label' => df_h()->banner()->__('Включен?')
					,'name'	=> 'status'
					,'values' =>
						array(
							array(
								'value' => 1
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
		if (df_mage()->adminhtml()->session()->getDfBannerData()) {
			$form->setValues(df_mage()->adminhtml()->session()->getDfBannerData());
			df_mage()->adminhtml()->session()->setDfBannerData(null);
		}
		else if (Mage::registry('df_banner_data')) {
			$form->setValues(Mage::registry('df_banner_data')->getData());
		}
		return parent::_prepareForm();
	}
}