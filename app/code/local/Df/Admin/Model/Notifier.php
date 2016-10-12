<?php
abstract class Df_Admin_Model_Notifier extends Df_Core_Model {
	/** @return string */
	abstract protected function getMessageTemplate();

	/** @return string */
	public function getMessage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_output()->processLink(
					strtr($this->getMessageTemplate(), $this->getMessageVariables())
					,$this->getUrlHelp()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getUrlSkip() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_url_admin(
					'df_admin/notification/skip'
					, array(Df_Admin_Model_Action_Notification_Skip::RP__CLASS => get_class($this))
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function needToShow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !Mage::getStoreConfigFlag($this->getConfigPathSkip());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Если администратор изменил значение наблюдаемой опции,
	 * то предшествующую команду администратора о скрытии предупреждения
	 * о проблемном значении этой опции считаем недействительной.
	 * Точно так же поступает и ядро Magento в сценарии предупреждений о настройках налогов:
	 * @see Mage_Tax_Model_Config_Notification::_resetNotificationFlag()
	 * @return void
	 */
	public function resetSkipStatus() {
		/** @var Df_Core_Model_Config_Data $config */
		$config = Df_Core_Model_Config_Data::i();
		$config->load($this->getConfigPathSkip(), 'path');
		$config->setValue(0);
		$config->setPath($this->getConfigPathSkip());
		$config->save();
	}

	/** @return array(string => string) */
	protected function getMessageVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				self::$MESSAGE_VAR__URL_HELP => $this->getUrlHelp()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getUrlHelp() {return '';}

	/** @return string */
	private function getConfigPathSkip() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::getConfigPathSkipByClass(get_class($this));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/** @var string */
	protected static $MESSAGE_VAR__URL_HELP = '{веб-адрес пояснений}';
	/**
	 * @param string $class
	 * @return string
	 */
	public static function getConfigPathSkipByClass($class) {return 'df/admin/notifiers/skip/' . $class;}
}