<?php
require_once BP . '/app/code/core/Mage/Adminhtml/controllers/Cms/WysiwygController.php';
class Df_Adminhtml_Cms_WysiwygController extends Mage_Adminhtml_Cms_WysiwygController {
	/**
	 * Template directives callback
	 * @override
	 */
	public function directiveAction() {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded = df_cfg()->admin()->editor()->fixHeadersAlreadySent();
		}
		if ($patchNeeded) {
			$this->directiveActionDf();
		}
		else {
			parent::directiveAction();
		}
	}

	/**
	 * Template directives callback
	 */
	private function directiveActionDf() {
		$directive = $this->getRequest()->getParam('___directive');
		$directive = df_mage()->coreHelper()->urlDecode($directive);
		/** @var string $url */
		$url = Df_Core_Model_Email_Template_Filter::i()->filter($directive);
		try {
			/*******************
			 * BEGIN PATCH
			 */
			$image = new Df_Varien_Image_Adapter_Gd2();
			$image->open($url);
			$this->getResponse()
				->setHeader(
					Zend_Http_Client::CONTENT_TYPE
					,$image->getMimeType()
				)
				->setBody($image->getOutput())
			;
			/********************
			 * END PATCH
			 */
		}
		catch(Exception $e) {
			/*******************
			 * BEGIN PATCH
			 */
			$image = new Df_Varien_Image_Adapter_Gd2();
			$image->open(Mage::getSingleton('cms/wysiwyg_config')->getSkinImagePlaceholderUrl());
			$this->getResponse()
				->setHeader(
					Zend_Http_Client::CONTENT_TYPE
					,$image->getMimeType()
				)
				->setBody($image->getOutput())
			;
			/********************
			 * END PATCH
			 */
		}
	}

}