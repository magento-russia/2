<?php
class Df_Adminhtml_Block_Widget_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/** @return bool|null */
	public function getUseAjax() {
		return $this->_getData(self::P__USE_AJAX);
	}

	/**
	 * @param bool $value
	 * @return Df_Adminhtml_Block_Widget_Tabs
	 */
	public function setUseAjax($value) {
		$this->setData(self::P__USE_AJAX, $value);
		return $this;
	}

	const _CLASS = __CLASS__;
	const P__USE_AJAX = 'use_ajax';

}