<?php
class Df_C1_Cml2Controller extends Mage_Core_Controller_Front_Action {
	/**
	 * Обратите внимание, что проверку на наличие и доступности лицензии
	 * мы выполняем не здесь, а в классе @see \Df\C1\Cml2\Action,
	 * потому что данные проверки должны при необходимости возбуждать исключительные ситуации,
	 * и именно в том классе расположен блок try... catch, который обрабатывает их
	 * надлежащим для 1C: Управление торговлей способом
	 * (возвращает диагностическое сообщение в 1C: Управление торговлей
	 * по стандарту CommerceML 2)
	 * @return void
	 */
	public function indexAction() {
		if (df_my_local()) {
			Mage::log(df_last(explode('?', df_ruri())));
		}
		df_action($this, \Df\C1\Cml2\Action\Front::class);
	}

	/**
	 * Инициализировать сессию надо именно здесь,
	 * иначе в родительском методе @see Mage_Core_Controller_Front_Action::preDispatch()
	 * сессия будет инициализирована стандартным образом, и она будет пустой,
	 * потому что стандартная процедура инициализации ничего не знает
	 * про передаваемый «1С:Управление торговлей» идентификатор сессии.
	 * http://dev.1c-bitrix.ru/api_help/sale/catalog_protocol.php
	 * @override
	 * @see Mage_Core_Controller_Front_Action::preDispatch()
	 * @used-by Mage_Core_Controller_Front_Action::dispatch()
	 * @return Df_C1_Cml2Controller
	 */
	public function preDispatch() {
		\Df\C1\Cml2\Session\ByCookie\C1::s();
		parent::preDispatch();
		return $this;
	}
}