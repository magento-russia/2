<?php
/**
 * Обратите внимание на очень интересную технику:
 * данный блок используется лишь для модификации другого блока.
 *
 * Данная техника является значительно более экономной по ресурсам,
 * нежели стандартная пока для Российской сборки техника подписки на событие и перебора всех блоков.
 *
 * Правда, техника применима только в том случае,
 * когда нуждающийся в модификации блок создан раньше вспомогательного.
 * Но для стандартных блоков это всегда так!
 *
 * Mage_Core_Block_Abstract::setLayout:
 *
 * [code]
		$this->_layout = $layout;
		Mage::dispatchEvent('core_block_abstract_prepare_layout_before', array('block' => $this));
		$this->_prepareLayout();
		Mage::dispatchEvent('core_block_abstract_prepare_layout_after', array('block' => $this));
		return $this;
 * [/code]
 *
 */
class Df_Cms_Block_Admin_Page extends Df_Core_Block_Admin {
	/**
	 * @override
	 * @return Df_Cms_Block_Admin_Page
	 */
	protected function _prepareLayout() {
		if (df_cfgr()->cms()->versioning()->isEnabled()) {
			/* @var Mage_Core_Block_Abstract $page */
			$page = $this->getLayout()->getBlock('cms_page');
			if ($page) {
				/* @var Mage_Adminhtml_Block_Cms_Page_Grid $pageGrid */
				$pageGrid = $page->getChild('grid');
				if ($pageGrid) {
					$pageGrid->addColumnAfter('versioned', array(
						'index' => 'under_version_control'
						,'header' => df_h()->cms()->__('Version Control?')
						,'width' => 10
						,'type' => 'options'
						,'options' => array(df_h()->cms()->__('No'),df_h()->cms()->__('Yes'))
					), 'page_actions');
				}
			}
		}
		return $this;
	}
}