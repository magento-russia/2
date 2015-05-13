<?php
/**
 * @method Df_Warehousing_Model_Warehouse getEntity()
 */
class Df_Warehousing_Admin_WarehouseController extends Df_Core_Controller_Admin_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getActionSaveClass() {
		return Df_Warehousing_Model_Action_Warehouse_Save::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getActiveMenuPath() {
		return 'catalog/df_warehouse';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {
		return Df_Warehousing_Model_Warehouse::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityTitle() {
		return $this->getEntity()->getName();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityTitleNew() {
		return 'Новый склад';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageDeleteSuccess() {
		return 'Склад удалён';
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getTitleParts() {
		return array('Каталог', 'Склады');
	}
}