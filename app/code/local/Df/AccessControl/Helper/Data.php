<?php
class Df_AccessControl_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_AccessControl_Model_Role|null */
	public function getCurrentRole() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				$this->getCurrentRoleId()
				? Df_AccessControl_Model_Role::ld($this->getCurrentRoleId())
				: null
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return int */
	public function getLastSavedRoleId() {return $this->_lastSavedRoleId;}

	/**
	 * @param int $roleId
	 * @return Df_AccessControl_Helper_Data
	 */
	public function setLastSavedRoleId($roleId) {$this->_lastSavedRoleId = $roleId;}

	/** @return int */
	private function getCurrentRoleId() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Admin_Model_User|null $user */
			$user = rm_admin_user(false);
			/**
			 * Здесь вполне может произойти исключительная ситуация
			 * по вине некачественных сторонних модулей,
			 * которые неправильно авторизуются в административной части
			 * (замечен дефект модуля Zizio Social Daily Deal)
			 *
			 * Раньше здесь стояло df_assert, но самопроверка может быть отключена администратором,
			 * а понятное сообщение здесь надо показать.
			 */
			if (!$user) {
				Mage::log(
					'Модуль «Управление административным доступом к товарным разделам» '
					. 'не в состоянии работать по причине некачественного стороннего модуля.'
				);
			}
			$this->{__METHOD__} = df_nat0($user->getRole()->getId());
		}
		return $this->{__METHOD__};
	}

	/** @var int */
	private $_lastSavedRoleId;

	/**
	 * @used-by Df_AccessControl_Model_Handler_Catalog_Category_Collection_ExcludeForbiddenCategories::handle()
	 * @param Varien_Data_Collection $c
	 * @param bool|null $value [optional]
	 * @return bool
	 */
	public static function disable($c, $value = null) {
		/** @var bool $result */
		$result = $c->hasFlag(__FUNCTION__);
		if (is_bool($value)) {
			$c->setFlag(__FUNCTION__, $value);
		}
		return $result;
	}

	/** @return Df_AccessControl_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}