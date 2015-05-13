<?php
class Df_AccessControl_Block_Admin_Tab
	extends Mage_Adminhtml_Block_Widget_Form
	implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	/** @return bool */
	public function canShowTab() {
		return
				df_enabled(Df_Core_Feature::ACCESS_CONTROL)
			&&
				df_cfg()->admin()->access_control()->getEnabled()
		;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTabLabel() {return self::LABEL;}

	/**
	 * @override
	 * @return string
	 */
	public function getTabTitle() {return $this->getTabLabel();}

	/**
	 * @override
	 * @return string|null
	 */
	public function getTemplate() {return !$this->canShowTab() ? null : 'df/access_control/tab.phtml';}

	/**
	 * @override
	 * @return boolean
	 */
	public function isHidden() {return false;}

	/** @return bool */
	public function isModuleEnabled() {return $this->getRole()->isModuleEnabled();}

	/** @return string */
	public function renderCategoryTree() {
		return df_block_render(new Df_AccessControl_Block_Admin_Tab_Tree());
	}

	/** @return string */
	public function renderStoreSwitcher() {
		return df_block_render('adminhtml/store_switcher', null, 'store/switcher/enhanced.phtml');
	}

	/** @return Df_AccessControl_Model_Role */
	private function getRole() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_AccessControl_Model_Role::i();
			// Обратите внимание,
			// что объект Df_AccessControl_Model_Role может отсутствовать в БД.
			// Видимо, это дефект моего программирования 2011 года.
			$this->{__METHOD__}->load($this->getRoleId());
		}
		return $this->{__METHOD__};
	}

	/** @return int|null */
	private function getRoleId() {
		/** @var int|null $result */
		$result = df_request('rid');
		if (!is_null($result)) {
			df_result_integer($result);
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	const LABEL = 'Доступ к товарным разделам';
}