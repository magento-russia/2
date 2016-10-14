<?php
class Df_Payment_Config_Area_Frontend extends Df_Payment_Config_Area {
	/** @return string */
	public function getDescription() {return $this->getVar('description', '');}

	/** @return string */
	public function getMessageFailure() {return $this->getVarWithDefaultConst('message_failure');}

	/** @return string */
	public function getTitle() {return $this->getVar('title', '');}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'frontend';}

	/**
	 * @override
	 * @return array
	 */
	protected function getStandardKeys() {
		return array_merge(parent::getStandardKeys(), array(
			'allowspecific', 'sort_order', 'specificcountry', 'title'
		));
	}
}