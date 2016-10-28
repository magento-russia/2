<?php
namespace Df\Pd4\Block\LinkToDocument;
class ForLastOrder extends \Df\Pd4\Block\LinkToDocument {
	/**
	 * @override
	 * @used-by \Df\Pd4\Block\LinkToDocument\ForLastOrder::getPaymentDocumentUrl()
	 * @used-by \Df\Pd4\Block\LinkToDocument\ForLastOrder::needToShow()
	 * @return \Df_Sales_Model_Order
	 */
	protected function order() {return df_last_order();}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/pd4/link_to_document/for_last_order.phtml';}
}