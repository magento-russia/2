<?php
abstract class Df_Adminhtml_Block_Widget_Form_Container
	extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getEntityClass();

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getNewEntityTitle();

	/**
	 * @override
	 * @return string
	 */
	public function getHeaderText() {
		/** @var string $result */
		$result =
				$this->getEntity()->getId()
			?
				$this->getEntity()->getName()
			:
				$this->getNewEntityTitle()
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		// Родительский конструктор обязательно должен вызываться перед нашим кодом!
		parent::_construct();
		$this->_blockGroup = $this->getBlockClassPrefix();
		// Используется нами для обозначения типа блока после косой черты, но до "edit".
		// Для контроллера не используется.
		$this->_controller = $this->getBlockClassSuffix();
		// А вот это используется для контроллера
		$this->setData('form_action_url', $this->getUrl('*/*/save'));
		$this
			->addButton(
				'save_and_edit_button'
				,array(
					'label' => 'сохранить и остаться'
					,'onclick' =>
						rm_sprintf(
							'editForm.submit(%s)'
							,df_quote_single($this->getSaveAndContinueUrl())
						)
					,'class' => 'save'
				)
				,1
			)
		;
	}

	/** @return string */
	private function getBlockClassPrefix() {
		return strtolower(df()->reflection()->getModuleName(get_class($this)));
	}

	/** @return string */
	private function getBlockClassSuffix() {
		/** @var string $result */
		$result =
			df_trim_suffix(
				rm_last(
					explode(
						Df_Core_Model_Reflection::MODULE_NAME_SEPARATOR
						,rm_class_mf(
							get_class(
								$this
							)
						)
					)
				)
				,'_edit'
			)
		;
		df_result_string($result);
		return $result;
	}

	/** @return Df_Core_Model_Entity */
	private function getEntity() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::registry($this->getEntityClass());
			df_assert($this->{__METHOD__} instanceof Df_Core_Model_Entity);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSaveAndContinueUrl() {
		return $this->getUrl('*/*/save', array('_current'  => true,'back' => 1));
	}

	const _CLASS = __CLASS__;
}