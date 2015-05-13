<?php
class Df_Admin_Model_Setup_2_23_7 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {$this->processRoles();}

	/**
	 * @return Df_Admin_Model_Setup_2_23_7
	 * @throws Exception
	 */
	private function processRoles() {
		foreach (Df_Admin_Model_Role::c() as $role) {
			/** @var Df_Admin_Model_Role $role */
			$role
				->setRoleName($this->translateRoleName($role->getRoleName()))
				->save()
			;
		}
		return $this;
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

	/** @return Df_Admin_Model_Setup_2_23_7 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}