<?php
class Df_Admin_Model_ClassRewrite_Collection extends Df_Varien_Data_Collection {
	/**
	 * @param string $type
	 * @param string $originClassNameMf
	 * @return Df_Admin_Model_ClassRewrite|null
	 */
	public function getByOrigin($type, $originClassNameMf) {
		return $this->getItemById(Df_Admin_Model_ClassRewrite::makeId($type, $originClassNameMf));
	}

	/** @return Df_Admin_Model_ClassRewrite_Collection */
	public function getConflicts() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Admin_Model_ClassRewrite_Collection $result */
			$result = self::i();
			foreach ($this as $rewrite) {
				/** @var Df_Admin_Model_ClassRewrite $rewrite */
				if ($rewrite->isConflict()) {
					$result->addItem($rewrite);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Admin_Model_ClassRewrite::class;}

	/** @used-by Df_Admin_Block_Notifier_ClassRewriteConflicts::_construct() */


	/** @return Df_Admin_Model_ClassRewrite_Collection */
	public static function i() {return new self;}
}