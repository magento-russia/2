<?php
class Df_Core_Model_Controller_Action_Admin_Entity_Delete
	extends Df_Core_Model_Controller_Action_Admin_Entity {
	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {
		df_assert(!is_null($this->getEntity()->getId()));
		try {
			$this->processDependencies();
			$this->getEntity()->delete();
			df_mage()->adminhtml()->session()->addSuccess($this->getMessageSuccess());
		}
		catch (Exception $e) {
			df_mage()->adminhtml()->session()->addError(rm_ets($e));
		}
		$this->redirect('*/*/');
		return '';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {
		return $this->cfg(self::P__ENTITY_CLASS);
	}

	/** @return string */
	private function getMessageSuccess() {
		return $this->cfg(self::P__MESSAGE_SUCCESS, 'Объект удалён');
	}

	/** @return Df_Core_Model_Controller_Action_Admin_Entity_Delete */
	private function processDependencies() {
		foreach ($this->getEntity()->getDependenciesInfo() as $dependencyInfo) {
			/** @var Df_Core_Model_Entity_Dependency $dependencyInfo */
			if ($dependencyInfo->needDeleteCascade()) {
				/** @var Df_Core_Model_Entity $dependency */
				$dependency = $this->getEntity()->getDependencyByName($dependencyInfo->getName());
				if (!is_null($dependency->getId())) {
					$dependency->delete();
					// Как ни странно — ядро Magento этого не делает.
					$dependency->unsetData();
				}
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__ENTITY_CLASS, self::V_STRING_NE)
			->_prop(self::P__MESSAGE_SUCCESS, self::V_STRING, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__ENTITY_CLASS = 'entity_class';
	const P__MESSAGE_SUCCESS = 'message_success';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Controller_Action_Admin_Entity_Delete
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}