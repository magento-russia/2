<?php
class Df_Admin_Setup_2_23_7 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		foreach (Df_Admin_Model_Role::c() as $role) {
			/** @var Df_Admin_Model_Role $role */
			$role->setRoleName($this->translateRoleName($role->getRoleName()));
			$role->save();
		}
	}

	/**
	 * @param string $name
	 * @param array(string => string) $dictionary
	 * @return string
	 */
	private function translate($name, array $dictionary) {return df_a($dictionary, $name, $name);}

	/**
	 * @param string $name
	 * @return string
	 */
	private function translateRoleName($name) {
		return $this->translate($name, array('Administrators' => 'администратор'));
	}
}