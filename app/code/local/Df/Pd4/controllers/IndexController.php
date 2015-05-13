<?php
/**
 * @method Df_Pd4_IndexController loadLayout()
 */
class Df_Pd4_IndexController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			$this
				->loadLayout()
				->preparePage()
				->renderLayout()
			;
		}
		catch(Exception $e) {
			df_notify_exception($e);
			rm_exception_to_session($e);
			$this->_redirect('');
		}
	}

	/** @return Df_Pd4_IndexController */
	private function preparePage() {
		df()->layout()->getBlockHead()->setTitle($this->getTitle());
		return $this;
	}

	/** @return string */
	private function getTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_sprintf(
					'Форма ПД-4 для заказа №%s'
					,$this->getOrder()->getDataUsingMethod(Df_Sales_Const::ORDER_PARAM__INCREMENT_ID)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {return $this->getAction()->getOrder();}

	/** @return Df_Pd4_Model_Request_Document_View */
	private function getAction() {return df_h()->pd4()->getDocumentViewAction();}
}