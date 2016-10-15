<?php
class Df_Localization_Onetime_Action extends Df_Core_Model_Action_Admin {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		$this->getProcessor()->process();
		if (!df_session()->getMessages()->getErrors()) {
			df_session()->addSuccess(sprintf(
				'Оформительская тема «%s» успешно русифицирована.', $this->getProcessor()->getTitle()
			));
		}
	}

	/** @return Df_Localization_Onetime_Processor */
	private function getProcessor() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Onetime_Processor_Collection::s()
					->getItemById($this->getProcessorId())
			;
			df_assert($this->{__METHOD__} instanceof Df_Localization_Onetime_Processor);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getProcessorId() {return df_request(self::RP__PROCESSOR);}

	const RP__PROCESSOR = 'processor';
}