<?php
class Df_Alfabank_Block_Info extends Df_Payment_Block_Info {
	/** @return Df_Alfabank_Model_Response_State */
	public function getState() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Alfabank_Model_Response_State::i();
			$this->{__METHOD__}->loadFromPaymentInfo($this->getInfo());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/alfabank/info.phtml';}

	const _CLASS = __CLASS__;
}