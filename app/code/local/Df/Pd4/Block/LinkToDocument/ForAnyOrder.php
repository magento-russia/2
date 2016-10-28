<?php
namespace Df\Pd4\Block\LinkToDocument;
class ForAnyOrder extends \Df\Pd4\Block\LinkToDocument {
	/**
	 * @override
	 * @see \Df\Pd4\Block\LinkToDocument::order()
	 * @used-by \Df\Pd4\Block\LinkToDocument::getPaymentDocumentUrl()
	 * @used-by \Df\Pd4\Block\LinkToDocument::needToShow()
	 * @return \Df_Sales_Model_Order
	 */
	public function order() {return $this[self::$P__ORDER];}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/pd4/link_to_document/for_any_order.phtml';}

	/** @var string */
	private static $P__ORDER = 'order';

	/**
	 * @used-by \Df\Pd4\Block\Info::getLinkToDocumentAsHtml()
	 * @param \Df_Sales_Model_Order $order
	 * @return string
	 */
	public static function r(\Df_Sales_Model_Order $order) {
		return df_render(new self(array(self::$P__ORDER => $order)));
	}
}