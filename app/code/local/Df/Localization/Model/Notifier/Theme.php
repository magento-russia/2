<?php
class Df_Localization_Model_Notifier_Theme extends Df_Admin_Model_Notifier {
	/**
	 * @override
	 * @return bool
	 */
	public function needToShow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::needToShow() && $this->getProcessors();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageTemplate() {
		return Df_Localization_Block_Admin_Theme_Notifier::render($this->getProcessors());
	}

	/** @return Df_Localization_Model_Onetime_Processor[] */
	private function getProcessors() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Localization_Model_Onetime_Processor[] */
			$result = array();
			foreach (Df_Localization_Model_Onetime_Processor_Collection::s() as $processor) {
				/** @var Df_Localization_Model_Onetime_Processor $processor */
				if ($processor->isApplicable()) {
					$result[]= $processor;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}