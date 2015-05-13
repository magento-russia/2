<?php
/**
 * @method Df_Pickup_Model_Point getEntity()
 * @method Df_Pickup_Model_Form_Point getForm()
 */
class Df_Pickup_Model_Action_Point_Save extends Df_Core_Model_Controller_Action_Admin_Entity_Save {
	/**
	 * @override
	 * @return Df_Pickup_Model_Action_Point_Save
	 */
	protected function entityUpdate() {
		$this->getEntity()
			->addData(
				array(
					Df_Pickup_Model_Point::P__NAME => $this->getForm()->getName()
					,Df_Pickup_Model_Point::P__LOCATION_ID => $this->getForm()->getLocationId()
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
		return Df_Pickup_Model_Point::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getFormClass() {
		return Df_Pickup_Model_Form_Point::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageSuccessForExistedEntity() {
		return 'Информация о пункте выдачи товара';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageSuccessForNewEntity() {
		return 'Пункт выдачи товара зарегистрирован';
	}

	const _CLASS = __CLASS__;
}