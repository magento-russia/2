<?php
class Df_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories {
	/**
	 * Цель перекрытия —
	 * недопустить привязку товара к тем товарным разделам,
	 * доступ к которым текущему администратору ограничем модулем Df_AccessControl.
	 * @override
	 * @param Varien_Data_Tree_Node $node
	 * @param int $level
	 * @return array(string => bool)
	 */
	protected function _getNodeJson($node, $level=1) {
		/** @var array(string => bool) $result */
		$result = parent::_getNodeJson($node, $level);
		if (
				df_module_enabled(Df_Core_Module::ACCESS_CONTROL)
			&&
				df_cfg()->admin()->access_control()->getEnabled()
			&&
				df_h()->accessControl()->getCurrentRole()->isModuleEnabled()
		) {
			if (
				!in_array(
					dfa($result, 'id')
					,df_h()->accessControl()->getCurrentRole()->getCategoryIds()
				)
			) {
				$result['disabled'] = true;
			}
		}
		return $result;
	}

}