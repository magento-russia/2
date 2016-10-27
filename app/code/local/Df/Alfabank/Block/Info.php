<?php
namespace Df\Alfabank\Block;
class Info extends \Df\Payment\Block\Info {
	/** @return \Df\Alfabank\Response\State */
	public function getState() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Alfabank\Response\State::i();
			$this->{__METHOD__}->loadFromPaymentInfo($this->getInfo());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/alfabank/info.phtml';}
}