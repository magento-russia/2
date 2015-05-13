<?php
abstract class Df_Qa_Model_Message extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getTemplate();

	/** @return string */
	public function getMessage() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Block_Template $block */
			$block =
				df_block('core/template', get_class($this), array(
					self::BLOCK_PARAM__MODEL => $this
					,'area' => 'frontend'
				))
			;
			$block->setTemplate($this->getTemplate());
			$this->{__METHOD__} = $block->toHtml();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Qa_Model_Message */
	public function log() {
		if ($this->needLogToFile()) {
			$this->writeToFile();
		}
		if ($this->needMail()) {
			$this->mail();
		}
		return $this;
	}

	/** @return string */
	private function getFileName() {return $this->cfg(self::P__FILE_NAME, 'rm-{date}--{time}.log');}

	/** @return Zend_Mail */
	private function getMail() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Mail $result */
			$result = new Zend_Mail('utf-8');
			$result
				->setFrom(df()->mail()->getCurrentStoreMailAddress())
				->setSubject($this->getMailSubject())
			;
			if ($this->needNotifyAdmin()) {
				$result->addTo(df()->mail()->getCurrentStoreMailAddress());
			}
			if ($this->needNotifyDeveloper()) {
				$result->addTo(self::DEVELOPER__MAIL_ADDRESS);
			}
			if (0 < count($this->getRecipients())) {
				$result->addTo($this->getRecipients());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Log */
	private function getMailLogger() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Log $result */
			$this->{__METHOD__} = new Zend_Log();
			$this->{__METHOD__}->addWriter($this->getMailWriter());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getMailSubject() {
		return str_replace('%domain%', df()->mail()->getCurrentStoreDomain(), self::T__MAIL__SUBJECT);
	}

	/** @return Zend_Log_Writer_Mail */
	private function getMailWriter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Log_Writer_Mail($this->getMail());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needMail() {
		return
				(0 < count($this->getRecipients()))
			||
				(
						!df_is_it_my_local_pc()
					&&
						($this->needNotifyAdmin() || $this->needNotifyDeveloper())
				)
		;
	}
	
	/** @return string[] */
	private function getRecipients() {return $this->cfg(self::P__RECIPIENTS, array());}

	/** @return Df_Qa_Model_Message */
	private function mail() {
		$this->getMailLogger()->log($this->getMessage(), Zend_Log::INFO);
		return $this;
	}

	/** @return bool */
	private function needLogToFile() {return $this->cfg(self::P__NEED_LOG_TO_FILE, false);}

	/** @return bool */
	private function needNotifyAdmin() {return $this->cfg(self::P__NEED_NOTIFY_ADMIN, false);}

	/** @return bool */
	private function needNotifyDeveloper() {return $this->cfg(self::P__NEED_NOTIFY_DEVELOPER, false);}

	/** @return Df_Qa_Model_Message */
	private function writeToFile() {
		df()->debug()->report($this->getFileName(), $this->getMessage());
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__FILE_NAME, self::V_STRING, false)
			->_prop(self::P__NEED_LOG_TO_FILE, self::V_BOOL)
			->_prop(self::P__NEED_NOTIFY_ADMIN, self::V_BOOL)
			->_prop(self::P__NEED_NOTIFY_DEVELOPER, self::V_BOOL)
			->_prop(self::P__RECIPIENTS, self::V_ARRAY, false)
		;
	}
	const _CLASS = __CLASS__;
	const BLOCK_PARAM__MODEL = 'model';
	const DEVELOPER__MAIL_ADDRESS = 'rm.bug.tracker@gmail.com';
	const P__FILE_NAME = 'file_name';
	const P__NEED_LOG_TO_FILE = 'need_log_to_file';
	const P__NEED_NOTIFY_ADMIN = 'need_notify_admin';
	const P__NEED_NOTIFY_DEVELOPER = 'need_notify_developer';
	const P__RECIPIENTS = 'recipients';
	const T__MAIL__SUBJECT = 'RM log: %domain%';
}