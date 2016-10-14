<?php
require_once BP . '/app/code/core/Mage/Adminhtml/controllers/Cms/WysiwygController.php';
class Df_Adminhtml_Cms_WysiwygController extends Mage_Adminhtml_Cms_WysiwygController {
	/**
	 * @override
	 * @see Mage_Adminhtml_Cms_WysiwygController::directiveAction()
	 * @return void
	 */
	public function directiveAction() {
		/** @var bool $patchNeeded */
		static $patchNeeded;
		if (is_null($patchNeeded)) {
			$patchNeeded = df_cfg()->admin()->editor()->fixHeadersAlreadySent();
		}
		$patchNeeded ? $this->directiveActionDf() : parent::directiveAction();
	}

	/** @return void */
	private function directiveActionDf() {
		/** @var string $directive */
		$directive = $this->getRequest()->getParam('___directive');
		$directive = df_mage()->coreHelper()->urlDecode($directive);
		/** @var string $url */
		$url = Df_Core_Model_Email_Template_Filter::i()->filter($directive);
		// начало заплатки
		try {
			$this->directiveActionDfInternal($url);
		}
		catch (Exception $e) {
			/** @var Mage_Cms_Model_Wysiwyg_Config $config */
			$config = Mage::getSingleton('cms/wysiwyg_config');
			$this->directiveActionDfInternal($config->getSkinImagePlaceholderUrl());
		}
		// конец заплатки
	}

	/**
	 * @param string $url
	 * @return void
	 */
	private function directiveActionDfInternal($url) {
		/** @var Df_Varien_Image_Adapter_Gd2 $image */
		$image = new Df_Varien_Image_Adapter_Gd2();
		$image->open($url);
		rm_response_content_type($this->getResponse(), $image->getMimeType());
		$this->getResponse()->setBody($image->getOutput());
	}
}