<?php
class Df_Payment_Model_Config_Area_Frontend extends Df_Payment_Model_Config_Area_Abstract {
	/** @return string */
	public function getDescription() {return $this->getVar(self::KEY__VAR__DESCRIPTION, '');}

	/** @return string */
	public function getMessageFailure() {return $this->getVarWithDefaultConst(self::KEY__MESSAGE_FAILURE);}

	/** @return string */
	public function getTitle() {return $this->getVar(self::KEY__VAR__TITLE, '');}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return self::AREA_PREFIX;}

	/**
	 * @override
	 * @return array
	 */
	protected function getStandardKeys() {
		return array_merge(parent::getStandardKeys(), array(
			'allowspecific', 'sort_order', 'specificcountry', 'title'
		));
	}

	const _CLASS = __CLASS__;
	const AREA_PREFIX = 'frontend';
	const KEY__MESSAGE_FAILURE = 'message_failure';
	const KEY__VAR__DESCRIPTION = 'description';
	const KEY__VAR__TITLE = 'title';
}