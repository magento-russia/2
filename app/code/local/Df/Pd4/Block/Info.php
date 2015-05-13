<?php
/**
 * @method Df_Pd4_Model_Payment getMethod()
 */
class Df_Pd4_Block_Info extends Df_Payment_Block_Info {
	/** @return string */
	public function getLinkToDocumentAsHtml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !$this->capableLinkToOrder() ? '' : $this->getLinkBlock()->toHtml();
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function capableLinkToOrder() {
		return $this->getOrder() && $this->getOrder()->getData(Df_Sales_Const::ORDER_PARAM__PROTECT_CODE);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/pd4/info.phtml';}

	/** @return Df_Pd4_Block_LinkToDocument_ForAnyOrder */
	private function getLinkBlock() {
		df_assert($this->capableLinkToOrder());
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Pd4_Block_LinkToDocument_ForAnyOrder::i();
			$this->{__METHOD__}->setOrder($this->getOrder());
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}