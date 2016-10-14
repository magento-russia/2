<?php
class Df_1C_Cml2Controller extends Mage_Core_Controller_Front_Action {
	/**
	 * Обратите внимание, что проверку на наличие и доступности лицензии
	 * мы выполняем не здесь, а в классе @see Df_1C_Cml2_Action,
	 * потому что данные проверки должны при необходимости возбуждать исключительные ситуации,
	 * и именно в том классе расположен блок try... catch, который обрабатывает их
	 * надлежащим для 1C: Управление торговлей способом
	 * (возвращает диагностическое сообщение в 1C: Управление торговлей
	 * по стандарту CommerceML 2)
	 * @return void
	 */
	public function indexAction() {
		if (df_is_it_my_local_pc()) {
			Mage::log(rm_last(explode('?', rm_ruri())));
		}
		rm_action($this, 'Df_1C_Cml2_Action_Front');
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
	 * @return Df_1C_Cml2Controller
	 */
	public function preDispatch() {
		Df_1C_Cml2_Session_ByCookie_1C::s();
		parent::preDispatch();
		return $this;
	}
}