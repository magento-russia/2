<?php
namespace Df\Pd4\Block;
/** @method \Df\Pd4\Method method() */
class Info extends \Df\Payment\Block\Info {
	/** @return string */
	public function getLinkToDocumentAsHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->capableLinkToOrder()
				? ''
				: LinkToDocument\ForAnyOrder::r($this->order())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function capableLinkToOrder() {
		return $this->order() && $this->order()->getProtectCode();
	}

	/**
	 * @override
	 * @see \Df_Core_Block_Template::defaultTemplate()
	 * @used-by \Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/pd4/info.phtml';}
}