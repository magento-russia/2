<?php
/**
 * @method Df_Core_Model_Location getEntity()
 * @method Df_Core_Model_Form_Location getForm()
 */
class Df_Core_Model_Action_Location_Save extends Df_Core_Model_Controller_Action_Admin_Entity_Save {
	/**
	 * @override
	 * @return Df_Core_Model_Action_Location_Save
	 */
	protected function entityUpdate() {
		$this->getEntity()
			->addData(
				array(
					Df_Core_Model_Location::P__CITY => $this->getForm()->getCity()
					,Df_Core_Model_Location::P__STREET_ADDRESS => $this->getForm()->getStreetAddress()
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
		return Df_Core_Model_Location::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getFormClass() {
		return Df_Core_Model_Form_Location::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageSuccessForExistedEntity() {
		return 'Информация о месте обновлена';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageSuccessForNewEntity() {
		return 'Место зарегистрировано';
	}

	const _CLASS = __CLASS__;
}