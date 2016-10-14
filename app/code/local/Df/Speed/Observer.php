<?php
class Df_Speed_Observer {
	/**
	 * 2015-08-03
	 * Обратите внимание,
	 * что при установке РСМ одновременно с CE
	 * controller_front_init_before — это первое событие,
	 * которое становится доступно подписчикам,
	 * а метод @see Df_Speed_Observer::controller_front_init_before()
	 * уже использует @uses df_cfg(),
	 * поэтому нам надо инициализирвать РСМ.
	 * Мы это делаем в методе @see Df_Core_Boot::controller_front_init_before()
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_front_init_before() {
		try {
			/**
			 * controller_front_init_before вызывается до создания объектов Zend_Date
			 * Включение кэша дат даёт прирост производительности
			 * только при использовании быстрой системы кеширования.
			 * Например:
			 * 	<cache>
					<backend>Zend_Cache_Backend_ZendServer_ShMem</backend>
					<prefix>MIR_PRYAZHI_</prefix>
				</cache>
			 */
			if (df_cfg()->speed()->general()->enableZendDateCaching()) {
				Zend_Date::setOptions(array('cache' => Mage::app()->getCache()));
			}
			if (
					/**
					 * Данная функциональность приводит к проблеме
					 * при добавлении товара к сравнению:
					 * http://magento-forum.ru/topic/2295/
					 */
					false
				&&
					df_cfg()->speed()->general()->disableVisitorLogging()
			) {
				/**
				 * На данном этапе инициализации системы
				 * мы не можем вызывать @see df_is_admin()
				 */
				/** @var bool $isFrontendArea */
				$isFrontendArea = !rm_contains(
					Mage::app()->getRequest()->getOriginalPathInfo()
					,rm_leaf_s(rm_config_node('admin/routers/adminhtml/args/frontName'))
				);
				if ($isFrontendArea) {
					/** @var Mage_Core_Model_Config_Element $eventsConfig */
					$eventsConfig = rm_config_node(Df_Core_Const_Design_Area::FRONTEND)->{'events'};
					/** @var SimpleXMLElement[]|bool $logNodes */
					$logNodes = $eventsConfig->xpath('//observers/log');
					if (is_array($logNodes)) {
						foreach ($logNodes as $node) {
							/** @var DOMElement|bool $domNode */
							$domNode = dom_import_simplexml($node);
							if ($domNode) {
								/** @var DOMNode $parent */
								$parent = $domNode->parentNode;
								$parent->removeChild($domNode);
							}
						}
					}
				}
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}