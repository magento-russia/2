<?php
/**
 * @method Df_Pickup_Model_Point getEntity()
 */
class Df_Pickup_Admin_PointController extends Df_Core_Controller_Admin_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function getActionSaveClass() {
		return Df_Pickup_Model_Action_Point_Save::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getActiveMenuPath() {
		return 'sales/df_point';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {
		return Df_Pickup_Model_Point::_CLASS;
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
		return 'Новый';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageDeleteSuccess() {
		return 'Пункт выдачи товара удалён';
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getTitleParts() {
		return array('Продажи', 'Пункты выдачи товара');
	}
}