<?php
class Df_Cms_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Cms_Model_Hierarchy_Node|null */
	public function getCurrentNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(Mage::registry('current_cms_hierarchy_node'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Mage_Cms_Model_Page|null */
	public function getCurrentPage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(Mage::registry('cms_page'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Cms_Model_Tree */
	public function getTree() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cms_Model_Tree::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Retrieve array of admin users in system
	 * @param bool $addEmptyUser [optional]
	 * @return array
	 */
	public function getUsersArray($addEmptyUser = false) {
		if (!$this->_usersHash) {
			$collection = Df_Admin_Model_User::c();
			$this->_usersHash = array();
			if ($addEmptyUser) {
				$this->_usersHash[''] = '';
			}
			foreach ($collection as $user) {
				/** @var Df_Admin_Model_User $user */
				$this->_usersHash[$user->getId()] = $user->getUsername();
			}
		}
		return $this->_usersHash;
	}
	/**
	 * Array of admin users in system
	 * @var array
	 */
	protected $_usersHash = null;

	/** @return string[] */
	public function getVersionAccessLevels() {
		return array(
			Df_Cms_Model_Page_Version::ACCESS_LEVEL_PRIVATE => $this->__('Private')
			,Df_Cms_Model_Page_Version::ACCESS_LEVEL_PROTECTED => $this->__('Protected')
			,Df_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC => $this->__('Public')
		);
	}

	/**
	 * Recursively walk through container (form or fieldset)
	 * and add to each element new onChange method.
	 * Element will be skipped if its type passed in $excludeTypes parameter.
	 *
	 * @param Varien_Data_Form_Abstract $container
	 * @param string $onChange
	 * @param string|array $excludeTypes
	 */
	public function addOnChangeToFormElements(
		$container
		,$onChange
		,$excludeTypes = array(Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN))
	{
		if (!is_array($excludeTypes)) {
			$excludeTypes = array($excludeTypes);
		}

		foreach ($container->getElements()as $element) {
			if ('fieldset' === $element->getType()) {
				$this->addOnChangeToFormElements($element, $onChange, $excludeTypes);
			} else {
				if (!in_array($element->getType(), $excludeTypes)) {
					if ($element->hasOnchange()) {
						$onChangeBefore = $element->getOnchange() . ';';
					} else {
						$onChangeBefore = '';
					}
					$element->setOnchange($onChangeBefore . $onChange);
				}
			}
		}
	}

	/** @return Df_Cms_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}