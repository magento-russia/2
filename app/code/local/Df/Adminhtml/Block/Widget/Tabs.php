<?php
class Df_Adminhtml_Block_Widget_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	/** @return string|null */
	public function getTitle() {
		return $this->_getData(self::P__TITLE);
	}

	/**
	 * @param string $value
	 * @return Df_Adminhtml_Block_Widget_Tabs
	 */
	public function setTitle($value) {
		$this->setData(self::P__TITLE, $value);
		return $this;
	}

	const _CLASS = __CLASS__;
	const P__TITLE = 'title';

}