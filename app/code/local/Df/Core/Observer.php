<?php
class Df_Core_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function admin_session_user_login_success() {
		try {
			if (file_exists($this->getJustInstalledFlagFilePath())) {
				unlink($this->getJustInstalledFlagFilePath());
				Df_Core_Setup_FirstRun::i()->process();
				Mage::dispatchEvent('df__magento_ce_has_just_been_installed');
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
	
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Core_Controller_Varien_Action::generateLayoutBlocks()
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.2.1/app/code/core/Mage/Core/Controller/Varien/Action.php#L349
		Mage::dispatchEvent(
			'controller_action_layout_generate_blocks_after',
			array('action'=>$this, 'layout'=>$this->getLayout())
		);
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_after() {
		df_state()->blocksHasBeenGenerated();
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_before() {
		df_state()->blocksGenerationStarted();
	}

	/** @return void */
	public function controller_action_layout_render_before() {
		try {df_state()->layoutRenderingHasBeenStarted();}
		catch(Exception $e) {df_handle_entry_point_exception($e);}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_action_postdispatch_install_wizard_end() {
		try {df_file_put_contents($this->getJustInstalledFlagFilePath(), 1);}
		catch (Exception $e) {df_handle_entry_point_exception($e);}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function controller_action_predispatch(Varien_Event_Observer $o) {
		// Как ни странно, ядро Magento этого не делает,
		// и поэтому диагностические сообщения валидиторов из Zend Framework оставались непереведёнными.
		// Ставим собаку, потому что иначе при переключении административной части
		// с русскоязычного интерфейса на англоязычный Zend Framework пишет:
		// «Notice: No translation for the language 'en' available.»
		// «Notice: The language 'en_US' has to be added before it can be used.»
		@Zend_Registry::set('Zend_Translate', Mage::app()->getTranslator()->getTranslate());
		try {
			df_state()->setController($o['controller_action']);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_front_send_response_after() {
		try {
			// Деинициализацию глобальных объектов-одиночек (например, сохранение кэша)
			// делаем не в деструкторе, а на событие «controller_front_send_response_after».
			// Полагаться на деструктор для таких объектов нельзя,
			// потому что к моменту вызова деструктора для данного объекта-одиночки
			// сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
			// требуемые для сохранения кэша.
			Df_Core_GlobalSingletonDestructor::s()->process();
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function core_block_abstract_to_html_after() {
		try {df_state()->blockSetPrev();}
		catch (Exception $e) {df_handle_entry_point_exception($e);}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_block_abstract_to_html_before(Varien_Event_Observer $o) {
		try {df_state()->blockSet($o['block']);}
		catch (Exception $e) {df_handle_entry_point_exception($e);}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @used-by Mage_Core_Model_Abstract::afterCommitCallback():
	 * Mage::dispatchEvent($this->_eventPrefix.'_save_commit_after', $this->_getEventData());
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_config_data_save_commit_after(Varien_Event_Observer $o) {
		try {
			/** @var Mage_Core_Model_Config_Data $config */
			$config = $o['data_object'];
			df_store()->setConfig($config->getPath(), $config->getValue());
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
	
	/** @return string */
	private function getJustInstalledFlagFilePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getBaseDir('var') . '/log/df__magento_ce_has_just_been_installed';
		}
		return $this->{__METHOD__};
	}
}