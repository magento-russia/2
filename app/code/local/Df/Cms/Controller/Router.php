<?php
class Df_Cms_Controller_Router extends Mage_Cms_Controller_Router {
	/**
	 * Validate and Match Cms Page and modify request
	 *
	 * @param Zend_Controller_Request_Http $request
	 * @return bool
	 */
	public function match(Zend_Controller_Request_Http $request)
	{
		if (!Mage::isInstalled()) {
			Mage::app()->getFrontController()->getResponse()
				->setRedirect(Mage::getUrl('install'))
				->sendResponse();
			exit;
		}
		/**************
		 * BEGIN PATCH
		 */
		/** @var string $identifier */
		$identifier = df_text()->trim(rawurldecode($request->getPathInfo()), '/');
		/**
		 * END PATCH
		 *************/
		$condition = new Varien_Object(array(
			'identifier' => $identifier,'continue'   => true
		));
		Mage::dispatchEvent('cms_controller_router_match_before', array(
			'router' => $this, 'condition' => $condition
		));
		$identifier = $condition->getIdentifier();
		if ($condition->getRedirectUrl()) {
			Mage::app()->getFrontController()->getResponse()
				->setRedirect($condition->getRedirectUrl())
				->sendResponse();
			$request->setDispatched(true);
			return true;
		}

		if (!$condition->getContinue()) {
			return false;
		}

		/** @var Df_Cms_Model_Page $page */
		$page = Df_Cms_Model_Page::i();
		$pageId = $page->checkIdentifier($identifier, Mage::app()->getStore()->getId());
		if (!$pageId) {
			return false;
		}
		$request->setModuleName('cms')
			->setControllerName('page')
			->setActionName('view')
			->setParam('page_id', $pageId);
		$request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier);
		return true;
	}

}