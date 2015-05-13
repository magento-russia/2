<?php
/**
 * @method Df_1C_Model_Cml2_InputRequest_Generic getRmRequest()
 */
abstract class Df_1C_Model_Cml2_Action extends Df_Core_Model_Controller_Action {
	/**
	 * @abstract
	 * @return void
	 */
	abstract protected function processInternal();

	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Action
	 */
	public function process() {
		try {
			/**
			 * Обратите внимание, что в программном коде, к сожалению, нельзя
			 * дополнительно отменить ограничение на max_input_time
			 * @link http://www.php.net/manual/en/info.configuration.php
			 */
			set_time_limit(0);
			ini_set('memory_limit', -1);
			rm_response_content_type($this->getResponse(), 'text/plain; charset=UTF-8');
			if (!df_enabled(Df_Core_Feature::_1C, rm_state()->getStoreProcessed())) {
				df_error($this->getText_noLicense());
			}
			if (!df_cfg()->_1c()->general()->isEnabled()) {
				df_error(self::T__MODULE_IS_DISABLED);
			}
			df_h()->dataflow()->registry()->products()->addValidator(Df_1C_Validate_Product::s());
			/** @var string|bool $output */
			$output = null;
			ob_start();
			$this->processInternal();
			/**
			 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
			 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru
			 * в другой точке программы при аналогичном вызове @see ob_get_clean.
			 */
			$output = @ob_get_clean();
			if ($output) {
				ob_start();
				df_error($output);
			}
			/**
			 * Запоминаем время последней успешной обработки данных
			 */
			if ($this->needUpdateLastProcessedTime()) {
				$this->updateLastProcessedTime();
			}
		}
		catch(Exception $e) {
			/** @var string $diagnosticMessage */
			$diagnosticMessage = rm_ets($e);
			/** @var string|bool $output */
			/**
			 * Используем @, чтобы избежать сбоя «Failed to delete buffer zlib output compression».
			 * Такой сбой у меня возник на сервере moysklad.magento-demo.ru.
			 */
			$output = @ob_get_clean();
			if ($output) {
				Mage::log('output buffer: ' . $output);
				$diagnosticMessage = $output;
			}
			df_handle_entry_point_exception($e, false);
			$this->setResponseBodyAsArrayOfStrings(array('failure', $diagnosticMessage));
		}
		return $this;
	}

	/** @return Zend_Date */
	protected function getLastProcessedTime() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Date $result */
			$result = null;
			/** @var string $time */
			$time = $this->getLastProcessedTimeAsString();
			if ($time) {
				try {
					// Мы вправе рассчитывать на стандартный для Zend_Date формат даты,
					// потому что предварительно именно в этом формате дата была сохранена.
					$result = new Zend_Date($time);
				}
				catch(Exception $e) {}
			}
			$this->{__METHOD__} = $result ? $result : df()->date()->getLeast();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_State */
	protected function getState() {return Df_1C_Model_Cml2_State::s();}

	/**
	 * @override
	 * @return string
	 */
	protected function getRmRequestClass() {return Df_1C_Model_Cml2_InputRequest_Generic::_CLASS;}

	/** @return Df_1C_Model_Cml2_Session_ByCookie_MagentoAPI */
	protected function getSessionMagentoAPI() {
		return Df_1C_Model_Cml2_Session_ByCookie_MagentoAPI::s();
	}

	/**
	 * @param string $paramName
	 * @param string|int $paramValue
	 * @return string
	 */
	protected function implodeResponseParam($paramName, $paramValue) {
		df_param_string_not_empty($paramName, 0);
		if (!is_int($paramValue)) {
			df_param_string($paramValue, 1);
		}
		return implode('=', array($paramName, $paramValue));
	}

	/** @return bool */
	protected function needUpdateLastProcessedTime() {return false;}

	/**
	 * @param string[] $responseData
	 * @return Df_1C_Model_Cml2_Action_Front
	 */
	protected function setResponseBodyAsArrayOfStrings(array $responseData) {
		rm_response_content_type($this->getResponse(), 'text/plain; charset=windows-1251');
		$this->getResponse()->setBody(df_text()->convertUtf8ToWindows1251(implode("\n", $responseData)));
		return $this;
	}

	/** @return string */
	private function getLastProcessedTimeAsString() {
		return
			df_nts(
				Mage::getStoreConfig(
					$this->getLastProcessedTimeConfigKey()
					,rm_state()->getStoreProcessed()
				)
			)
		;
	}

	/**
	 * Формирует ключ вида «df_1c/last_process_time/<уникальный суффикс>», * например:
	 * «df_1c/last_process_time/orders_export»
	 * «df_1c/last_process_time/orders_import»
	 * «df_1c/last_process_time/catalog_import»
	 * «df_1c/last_process_time/catalog_upload»
	 *
	 * Реально нам эти ключи нужны пока только для заказов.
	 * @return string
	 */
	private function getLastProcessedTimeConfigKey() {
		return rm_config_key(
			'df_1c', 'last_process_time', $this->getLastProcessedTimeConfigKeySuffix()
		);
	}

	/** @return string */
	private function getLastProcessedTimeConfigKeySuffix() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_concat(
					/**
					 * Нам нужно два символа «_» подряд, чтобы ключ имел вид:
					 * «df_1c/last_process_time/»
					 */
					'_'
					,rm_config_key(
						explode(
							'_'
							,mb_strtolower(
								str_replace(get_class(), '', get_class($this))
							)
						)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string|string[] $responseData
	 * @return Df_1C_Model_Cml2_Action_Front
	 */
	private function setResponseFailure($responseData) {
		if (!is_array($responseData)) {
			df_param_string($responseData, 0);
			$responseData = array($responseData);
		}
		array_unshift($responseData, 'failure');
		$this->setResponseBodyAsArrayOfStrings($responseData);
		return $this;
	}

	/** @return string */
	private function getText_noLicense() {
		return
			'У маг' . 'азин' . 'а отсут' . 'ствуе' . 'т лице' . 'нзия '
			. 'на ис' . 'польз' . 'овани' . 'е мо' . 'дуля «1' . 'C:Уп' . 'равле'
			. 'ние то' . 'ргов' . 'лей»'
		;
	}

	/**
	 * Учитываем время последней успешной обработки данных.
	 * Это время нам нужно, например, в сценариях обработки заказов,
	 * потому что магазин должен передавать в 1С: Управление торговлей 2 вида заказов,
	 * и для определения обоих видов используется время последнего сеанса передачи данных:
	 * 1) Заказы, которые были созданы в магазине ПОСЛЕ последнего сеанса передачи данных
	 * 2) Заказы, которые были изменены в магазине ПОСЛЕ последнего сеанса передачи данных
	 * @return Df_1C_Model_Cml2_Action
	 */
	private function updateLastProcessedTime() {
		$this->{__CLASS__ . '::getLastProcessedTime'} = new Zend_Date(Zend_Date::now());
		df_h()->_1c()->cml2()->setStoreProcessedConfigValue(
			$path = $this->getLastProcessedTimeConfigKey()
			,$value = df_dts($this->getLastProcessedTime())
		);
		return $this;
	}

	const _CLASS = __CLASS__;
	const T__MODULE_IS_DISABLED =
		'Модуль «1C:Управление торговлей» отключен в административной части магазина'
	;
}