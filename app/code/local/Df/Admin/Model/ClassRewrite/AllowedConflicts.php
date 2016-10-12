<?php
class Df_Admin_Model_ClassRewrite_AllowedConflicts extends Df_Core_Model {
	/**
	 * @param string $active
	 * @param string $inactive
	 * @return bool
	 */
	public function isAllowed($active, $inactive) {
		/** @var string[]|null $inactives */
		$inactives = df_a($this->getMap(), $active);
		return $inactives && isset($inactives[$inactive]);
	}

	/** @return array(string => array(string => bool) */
	private function getMap() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => array(string => bool) $result */
			$result = array();
			/** @var Mage_Core_Model_Config_Element[]|bool $nodes */
			$nodes = Mage::getConfig()->getNode('df/admin/class_rewrite_conflicts/skip/conflict');
			if ($nodes) {
				foreach ($nodes as $node) {
					/** @var Mage_Core_Model_Config_Element $node */
					/** @var string $active */
					$active = (string)$node->{'active'};
					df_assert_string_not_empty($active);
					/** @var string $inactive */
					$inactive = (string)$node->{'inactive'};
					df_assert_string_not_empty($inactive);
					$result[$active][$inactive]= true;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return self::m(__CLASS__, 'getMap');}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return $this->getPropertiesToCache();}

	/** @return Df_Admin_Model_ClassRewrite_AllowedConflicts */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}