<?php
class Df_AccessControl_Model_Event_Permissions_Role_Saverole
	extends Df_Core_Model_Event_Controller_Action_Postdispatch {
	/** @return bool */
	public function isModuleEnabledForRole() {
		return df_bool($this->getController()->getRequest()->getParam('df_accessControl_enable'));
	}

	/** @return int */
	public function getRoleId() {
		/** @var int $result */
		$result = $this->getController()->getRequest()->getParam('role_id');
		if (!$result) {
			// Сюда мы попадаем при первом сохранении новой роли
			$result = df_h()->accessControl()->getLastSavedRoleId();
		}
		df_result_integer($result);
		return $result;
	}

	/** @return int[] */
	public function getSelectedCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$this->{__METHOD__} = df_csv_parse_int($this->getSelectedCategoryIdsAsString());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSelectedCategoryIdsAsString() {
		return $this->getController()->getRequest()->getParam('df_accessControl_selectedCategories', '');
	}

	/**
	 * @used-by Df_AccessControl_Observer::controller_action_postdispatch_adminhtml_permissions_role_saverole()
	 * @used-by Df_AccessControl_Model_Handler_Permissions_Role_Saverole_UpdateCatalogAccessRights::getEventClass()
	 */
	const _C = __CLASS__;
}