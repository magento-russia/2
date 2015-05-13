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
class Df_Cms_Block_Adminhtml_Cms_Page extends Df_Core_Block_Admin {
	/**
	 * Add  column Versioned to cms page grid
	 * @return Df_Cms_Block_Adminhtml_Cms_Page
	 */
	protected function _prepareLayout()
	{
		if (
				df_cfg()->cms()->versioning()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::CMS_2)
		) {
			/* @var $pageGrid Mage_Adminhtml_Block_Cms_Page_Grid */
			$page = $this->getLayout()->getBlock('cms_page');
			if ($page) {
				$pageGrid = $page->getChild('grid');
				if ($pageGrid) {
					$pageGrid->addColumnAfter(
						'versioned'
						, array(
							'index' => 'under_version_control'
							,'header' => df_h()->cms()->__('Version Control?')
							,'width' => 10
							,'type' => 'options'
							,'options' => array(df_h()->cms()->__('No'),df_h()->cms()->__('Yes')
						)
					), 'page_actions');
				}
			}
		}
		return $this;
	}
}