<?php
abstract class Df_Qa_Message extends Df_Core_Model {
	/**
	 * @used-by report()
	 * @return string
	 */
	abstract protected function main();

	/**
	 * @used-by df_notify_exception()
	 * @used-by Df_Qa_Message_Failure_Error::check()
	 * @return void
	 * @throws Exception
	 */
	public final function log() {
		/**
		 * 2015-04-04
		 * Нам нужно правильно обработать ситуацию,
		 * когда при формировании диагностического отчёта о сбое происходит новый сбой.
		 * 1) Статическая переменная $inProcess предотвращает нас от бесконечной рекурсии.
		 * 2) try... catch позволяет нам перехватить внутренний сбой,
		 * сформировать диагностическое сообщение о нём,
		 * а затем перевозбудить его снова, чтобы вывести на экран.
		 * Обратите внимание, что внутренний сбой не будет виден на экране при асинхронном запросе
		 * (много таких запросов делает, например, страница оформления заказа),
		 * поэтому try... catch с целью записи отчёта крайне важно:
		 * без этого при сбое асинхроноого запроса диагностичекское сообщение о сбое
		 * окажется утраченным.
		 */
		static $inProcess;
		if (!$inProcess) {
			$inProcess = true;
			try {
				if ($this[self::P__NEED_LOG_TO_FILE]) {
					rm_report($this->cfg(self::P__FILE_NAME, 'rm-{date}--{time}.log'), $this->report());
				}
				if ($this->needMail()) {
					$this->mail();
				}
				$inProcess = false;
			}
			catch (Exception $e) {
				Mage::logException($e);
				throw $e;
			}
		}
	}

	/**
	 * @used-by report()
	 * @return string
	 */
	protected function postface() {return '';}

	/**
	 * @used-by report()
	 * @return string
	 */
	protected function preface() {return '';}

	/**
	 * @used-by Df_Qa_Message_Failure::traceS()
	 * @used-by Df_Qa_Message_Failure_Exception::preface()
	 * @used-by report()
	 * @param string|string[] $items
	 * @return string
	 */
	protected function sections($items) {
		if (!is_array($items)) {
			$items = func_get_args();
		}
		/** @var string $s */
		static $s; if (!$s) {$s = "\n" . str_repeat('*', 36) . "\n";};
		return implode($s, array_filter(df_trim(rm_xml_output_plain($items))));
	}

	/** @return Zend_Mail */
	private function createMail() {
		/** @var Zend_Mail $result */
		$result = new Zend_Mail('utf-8');
		$result
			->setFrom(df()->mail()->getCurrentStoreMailAddress())
			->setSubject('RM log: ' . df()->mail()->getCurrentStoreDomain())
		;
		if ($this->needNotifyAdmin()) {
			$result->addTo(df()->mail()->getCurrentStoreMailAddress());
		}
		if ($this->needNotifyDeveloper()) {
			$result->addTo('rm.bug.tracker@gmail.com');
		}
		if ($this->recipients()) {
			$result->addTo($this->recipients());
		}
		return $result;
	}

	/**
	 * @used-by log()
	 * @return bool
	 */
	private function needMail() {
		return
			$this->recipients()
			|| (!df_is_it_my_local_pc() && ($this->needNotifyAdmin() || $this->needNotifyDeveloper()))
		;
	}

	/**
	 * @used-by log()
	 * @return void
	 */
	private function mail() {
		/** @var Zend_Log $logger */
		$logger = new Zend_Log();
		$logger->addWriter(new Zend_Log_Writer_Mail($this->createMail()));
		$logger->log($this->report(), Zend_Log::INFO);
	}

	/**
	 * @used-by log()
	 * @used-by mail()
	 * @return string
	 */
	private function report() {
		if (!isset($this->{__METHOD__})) {
			rm_context(
				array('URL', df_current_url(), -100)
				,array('Версия Magento', rm_version_full(), -99)
				,array('Версия PHP', phpversion(), -98)
				,array('Время', df()->date()->nowInMoscowAsText(), -97)
			);
			if (!df_is_admin()) {
				/** @var string $rmDesignPackage */
				$rmDesignPackage = rm_state()->getCurrentDesignPackage();
				/** @var string $rmDesignTheme */
				$rmDesignTheme = rm_state()->getCurrentDesignTheme();
				rm_context('Оформительская тема', "{$rmDesignPackage} / {$rmDesignTheme}", -96);
			}
			$this->{__METHOD__} = $this->sections(
				Df_Qa_Context::render(), $this->preface(), $this->main(), $this->postface()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by createMail()
	 * @used-by needMail()
	 * @return bool
	 */
	private function needNotifyAdmin() {return $this[self::P__NEED_NOTIFY_ADMIN];}

	/**
	 * @used-by createMail()
	 * @used-by needMail()
	 * @return bool
	 */
	private function needNotifyDeveloper() {return $this[self::P__NEED_NOTIFY_DEVELOPER];}

	/**
	 * @used-by createMail()
	 * @used-by needMail()
	 * @return string[]
	 */
	private function recipients() {return $this->cfg(self::P__RECIPIENTS, array());}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__FILE_NAME, DF_V_STRING, false)
			->_prop(self::P__NEED_LOG_TO_FILE, DF_V_BOOL)
			->_prop(self::P__NEED_NOTIFY_ADMIN, DF_V_BOOL)
			->_prop(self::P__NEED_NOTIFY_DEVELOPER, DF_V_BOOL)
			->_prop(self::P__RECIPIENTS, DF_V_ARRAY, false)
		;
	}
	const P__FILE_NAME = 'file_name';
	const P__NEED_LOG_TO_FILE = 'need_log_to_file';
	const P__NEED_NOTIFY_ADMIN = 'need_notify_admin';
	const P__NEED_NOTIFY_DEVELOPER = 'need_notify_developer';
	const P__RECIPIENTS = 'recipients';
}