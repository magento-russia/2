<?php
class Df_AccessControl_Model_Event_Permissions_Role_Saverole
	extends Df_Core_Model_Event_Controller_Action_Postdispatch {
	/** @return bool */
	public function isModuleEnabledForRole() {
		return rm_bool($this->getController()->getRequest()->getParam(self::REQUEST_PARAM__ENABLE));
	}

	/** @return int */
	public function getRoleId() {
		/** @var int $result */
		$result =
			$this->getController()->getRequest()->getParam(
				self::REQUEST_PARAM__ROLE_ID
			)
		;
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
			$this->{__METHOD__} = df_parse_csv($this->getSelectedCategoryIdsAsString());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSelectedCategoryIdsAsString() {
		return $this->getController()->getRequest()->getParam(
			self::REQUEST_PARAM__DF_ACCESSCONTROL__SELECTEDCATEGORIES, ''
		);
	}

	const _CLASS = __CLASS__;
	const REQUEST_PARAM__DF_ACCESSCONTROL__SELECTEDCATEGORIES = 'df_accessControl_selectedCategories';
	const REQUEST_PARAM__ENABLE = 'df_accessControl_enable';
	const REQUEST_PARAM__ROLE_ID = 'role_id';
}