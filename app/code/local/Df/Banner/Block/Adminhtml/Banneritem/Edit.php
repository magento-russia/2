<?php
class Df_Banner_Block_Adminhtml_Banneritem_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Перекрывать надо именно конструктор, а не метод _construct,
	 * потому что родительский класс пихает инициализацию именно в конструктор.
	 * @override
	 * @return Df_Banner_Block_Adminhtml_Banneritem_Edit
	 */
	public function __construct() {
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'df_banner';
		$this->_controller = 'adminhtml_banneritem';
		$this->_updateButton('save', 'label', df_h()->banner()->__('Утвердить и вернуться'));
		$this->_updateButton('delete', 'label', df_h()->banner()->__('Удалить'));
		$this
			->_addButton(
				'saveandcontinue'
				,array(
					'label'	=> df_mage()->adminhtml()->__('Утвердить и остаться')
					,'onclick' => 'saveAndContinueEdit()'
					,'class' => 'save'
				)
				,-100
			)
		;
		$this->_formScripts[]= "
			function toggleEditor() {
				if (null === tinyMCE.getInstanceById('df_banner_content')) {
					tinyMCE.execCommand('mceAddControl', false, 'df_banner_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'df_banner_content');
				}
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	/**
	 * @override
	 * @return string
	 */
	public function getHeaderText() {
		/** @var string $result */
		$result = '';
		if (Mage::registry('df_banner_item_data') && Mage::registry('df_banner_item_data')->getId()) {
			$result =
				df_h()->banner()->__(
					'Объявление «%s»'
					,$this->escapeHtml(
						Mage::registry('df_banner_item_data')->getTitle()
					)
				)
			;
		} else {
			$result = df_h()->banner()->__('Составить новое объявление...');
		}
		return $result;
	}

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
}