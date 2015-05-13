<?php
class Df_Review_Block_Frontend_Js extends Df_Core_Block_Template {
	/**
	 * @override
	 * @return Df_Review_Block_Frontend_Js
	 */
	protected function _prepareLayout() {
		if (df_magento_version('1.9', '>')) {
			/** @var Df_Page_Block_Html_Head $head */
			$head = $this->getLayout()->getBlock('head');
			if ($head) {
				$head->addItem(
					$type = 'skin_js'
					, $name = 'js/lib/elevatezoom/jquery.elevateZoom-3.0.8.min.js'
				);
			}
		}
		parent::_prepareLayout();
		return $this;
	}
}