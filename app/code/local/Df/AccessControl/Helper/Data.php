<?php
class Df_AccessControl_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_AccessControl_Model_Role|null */
	public function getCurrentRole() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_AccessControl_Model_Role|null $result */
			$result = null;
			/** @var int|null $roleId */
			$roleId = null;
			/**
			 * Здесь вполне может произойти исключительная ситуация
			 * по вине некачественных сторонних модулей,
			 * которые неправильно авторизуются в административной части
			 * (замечен дефект модуля Zizio Social Daily Deal)
			 */
			try {
				$roleId = $this->getCurrentRoleId();
			}
			catch(Exception $e) {
				Mage::log(
					'Модуль «Управление административным доступом к товарным разделам» '
					.'не в состоянии работать по причине некачественного стороннего модуля.'
				);
			}
			if ($roleId) {
				df_assert_integer($roleId);
				/** @var Df_AccessControl_Model_Role $result */
				$result = Df_AccessControl_Model_Role::i();
				/**
				 * Обратите внимание,
				 * что объект Df_AccessControl_Model_Role может отсутствовать в БД.
				 * Видимо, это дефект моего программирования 2011 года.
				 */
				$result->load($this->getCurrentRoleId());
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return int */
	public function getLastSavedRoleId() {
		/** @var int $result */
		$result = $this->_lastSavedRoleId;
		df_result_integer($result);
		return $result;
	}

	/**
	 * @param int $roleId
	 * @return Df_AccessControl_Helper_Data
	 */
	public function setLastSavedRoleId($roleId) {
		df_param_integer($roleId, 0);
		$this->_lastSavedRoleId = $roleId;
		$this->getLastSavedRoleId();
		return $this;
	}
	/** @var int */
	private $_lastSavedRoleId;

	/**
	 * @throws Exception
	 * @return int
	 */
	private function getCurrentRoleId() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Admin_Model_User $user */
			$user = df_mage()->admin()->session()->getDataUsingMethod(Df_Admin_Const::SESSION__PARAM__USER);
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
				df_error();
			}
			$this->{__METHOD__} = rm_nat0($user->getRole()->getId());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_AccessControl_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}