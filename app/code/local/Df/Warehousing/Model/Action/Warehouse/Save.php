<?php
/**
 * @method Df_Warehousing_Model_Warehouse getEntity()
 * @method Df_Warehousing_Model_Form_Warehouse getForm()
 */
class Df_Warehousing_Model_Action_Warehouse_Save
	extends Df_Core_Model_Controller_Action_Admin_Entity_Save {
	/**
	 * @override
	 * @return Df_Warehousing_Model_Action_Warehouse_Save
	 */
	protected function entityUpdate() {
		$this->getEntity()
			->addData(
				array(
					Df_Warehousing_Model_Warehouse::P__NAME => $this->getForm()->getName()
					,Df_Warehousing_Model_Warehouse::P__LOCATION_ID =>
						$this->getForm()->getLocationId()
				)
			)
		;
		return $this;
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
	protected function getFormClass() {
		return Df_Warehousing_Model_Form_Warehouse::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageSuccessForExistedEntity() {
		return 'Информация о складе обновлена';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageSuccessForNewEntity() {
		return 'Склад зарегистрирован';
	}

	const _CLASS = __CLASS__;
}