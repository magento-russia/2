<?php
class Df_Admin_Model_ClassRewrite_AllowedConflicts extends Df_Core_Model {
	/**
	 * @param string $active
	 * @param string $inactive
	 * @return bool
	 */
	public function isAllowed($active, $inactive) {
		/** @var string[]|null $inactives */
		$inactives = dfa($this->getMap(), $active);
		return $inactives && isset($inactives[$inactive]);
	}

	/**
	 * @override
	 * @see Df_Core_Model::cached()
	 * @return string[]
	 */
	protected function cached() {return self::m(__CLASS__, 'getMap');}

	/** @return array(string => array(string => bool) */
	private function getMap() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => array(string => bool) $result */
			$result = [];
			/** @var Mage_Core_Model_Config_Element[]|bool $nodes */
			$nodes = df_config_node('df/admin/class_rewrite_conflicts/skip/conflict');
			if ($nodes) {
				foreach ($nodes as $node) {
					/** @var Mage_Core_Model_Config_Element $node */
					/** @var string $active */
					$active = df_leaf_sne($node->{'active'});
					/** @var string $inactive */
					$inactive = df_leaf_sne($node->{'inactive'});
					$result[$active][$inactive] = true;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Model_ClassRewrite_AllowedConflicts */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}