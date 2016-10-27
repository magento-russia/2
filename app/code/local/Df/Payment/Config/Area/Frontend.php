<?php
namespace Df\Payment\Config\Area;
class Frontend extends \Df\Payment\Config\Area {
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