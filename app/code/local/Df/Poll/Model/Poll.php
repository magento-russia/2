<?php
class Df_Poll_Model_Poll extends Mage_Poll_Model_Poll {
	/**
	 * @override
	 * @return Mage_Poll_Model_Poll_Answer[]|Df_Poll_Model_Resource_Poll_Answer_Collection
	 */
	public function getAnswers() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Poll_Model_Poll_Answer[]|Df_Poll_Model_Resource_Poll_Answer_Collection $result */
			$result = parent::getAnswers();
			if (!$result && $this->getId()) {
				$result = Df_Poll_Model_Poll_Answer::c();
				$result->addPollFilter($this->getId());
				Df_Varien_Data_Collection::unsetDataChanges($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Poll_Model_Resource_Poll_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Poll_Model_Resource_Poll
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Poll_Model_Resource_Poll::s();}

	/**
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Poll::getEntityClass()
	 * @used-by Df_Poll_Model_Resource_Poll_Collection::_construct()
	 */
	const _C = __CLASS__;
	/**
	 * @static
	 * @param bool $loadStoresInfo [optional]
	 * @return Df_Poll_Model_Resource_Poll_Collection
	 */
	public static function c($loadStoresInfo = false) {
		return Df_Poll_Model_Resource_Poll_Collection::i($loadStoresInfo);
	}
	/** @return Df_Poll_Model_Poll */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}