<?php
class Df_Cms_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Cms_Model_Hierarchy_Node|null */
	public function getCurrentNode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(Mage::registry('current_cms_hierarchy_node'));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Mage_Cms_Model_Page|null */
	public function getCurrentPage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(Mage::registry('cms_page'));
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Df_Cms_Model_Tree */
	public function getTree() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cms_Model_Tree::i();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @uses Df_Admin_Model_User::getId()
	 * @uses Df_Admin_Model_User::getUsername()
	 * @param bool $addEmptyUser [optional]
	 * @return array(int|string => string)
	 */
	public function getUsersArray($addEmptyUser = false) {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => string) $result */
			$result = df_column(Df_Admin_Model_User::c(), 'getUsername', 'getId');
			if ($addEmptyUser) {
				$result = array('' => '') + $result;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

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
	 * @param string|string[] $excludeTypes
	 */
	public function addOnChangeToFormElements(
		$container
		,$onChange
		,$excludeTypes = array(Df_Varien_Data_Form_Element_Abstract::TYPE__HIDDEN))
	{
		$excludeTypes = df_array($excludeTypes);
		foreach ($container->getElements()as $element) {
			if ('fieldset' === $element->getType()) {
				$this->addOnChangeToFormElements($element, $onChange, $excludeTypes);
			}
			else {
				if (!in_array($element->getType(), $excludeTypes)) {
					if ($element->hasOnchange()) {
						$onChangeBefore = $element->getOnchange() . ';';
					}
					else {
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