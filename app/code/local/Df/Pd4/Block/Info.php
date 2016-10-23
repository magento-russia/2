<?php
/** @method Df_Pd4_Method getMethod() */
class Df_Pd4_Block_Info extends Df_Payment_Block_Info {
	/** @return string */
	public function getLinkToDocumentAsHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->capableLinkToOrder()
				? ''
				: Df_Pd4_Block_LinkToDocument_ForAnyOrder::r($this->order())
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
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/pd4/info.phtml';}
}