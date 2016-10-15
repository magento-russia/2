<?php
/**
 * 2015-03-16
 * @used-by Mage_Core_Model_App::_callObserverMethod()
 * Этот класс добавляется в список обработчиков запросов через подписку на событие
 * в файле Df/Cms/etc/config.xml:
	<controller_front_init_routers>
		<observers>
			<Df_Cms>
				<!-- Для поддержки кириллицы в адресах самодельных страниц -->
				<class>Df_Cms_Controller_Router</class>
				<method>initControllerRouters</method>
			</Df_Cms>
		</observers>
	</controller_front_init_routers>
 * @see Mage_Cms_Controller_Router::initControllerRouters
 */
class Df_Cms_Controller_Router extends Mage_Cms_Controller_Router {
	/**
	 * Цель перекрытия — поддержка кириллицы в адресах самодельных страниц.
	 * @override
	 * @see Mage_Cms_Controller_Router::match()
	 * @param Zend_Controller_Request_Http $request
	 * @return bool
	 */
	public function match(Zend_Controller_Request_Http $request) {
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
		$identifier = df_t()->trim(rawurldecode($request->getPathInfo()), '/');
		/**
		 * END PATCH
		 *************/
		$condition = new Varien_Object(array('identifier' => $identifier, 'continue' => true));
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
		$pageId = $page->checkIdentifier($identifier, df_store_id());
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