<?php
class Df_Core_Model_Fs_GetNotUsedFileName extends Df_Core_Model_Abstract {
	/** @return string */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			/** @var int $counter */
			$counter = 1;
			/** @var bool $hasOrderingPosition */
			$hasOrderingPosition = rm_contains($this->getNameTemplate(), '{ordering}');
			while (true) {
				/** @var string $fileName */
				$fileName = strtr($this->getNameTemplate(), array_merge(
					array('{ordering}' => rm_sprintf('%03d', $counter))
					, $this->getVariables()
				));
				/** @var string $fileFullPath */
				$fileFullPath = $this->getDirectory() . DS . $fileName;
				if (!file_exists($fileFullPath)) {
					/**
					 * Раньше здесь стояло file_put_contents,
					 * и иногла почему-то возникал сбой:
					 * failed to open stream: No such file or directory.
					 * Может быть, такой сбой возникает, если папка не существует?
					 */
					$result = $fileFullPath;
					break;
				}
				else {
					if ($counter > $this->getCounterLimit()) {
						/** @var string $diagnosticMessage */
						$diagnosticMessage = rm_sprintf('Счётчик достиг предела (%d).', $counter);
						if ($this->needThrowOnReachCounterLimit()) {
							df_error($diagnosticMessage);
						}
						else {
							Mage::log($diagnosticMessage);
							break;
						}
					}
					else {
						$counter++;
						/**
						 * Если в шаблоне имени файла
						 * нет переменной «{ordering}» — значит, надо добавить её,
						 * чтобы в следующей интерации имя файла стало уникальным.
						 * Вставляем «{ordering}» непосредственно перед расширением файла.
						 * Например, rm.shipping.log преобразуем в rm.shipping-{ordering}.log
						 */
						if (!$hasOrderingPosition && (2 === $counter)) {
							/** @var string[] $fileNameTemplateExploded */
							$fileNameTemplateExploded = explode('.', $this->getNameTemplate());
							/** @var int $secondFromLastPartIndex*/
							$secondFromLastPartIndex =  max(0, count($fileNameTemplateExploded) - 2);
							/** @var string $secondFromLastPart */
							$secondFromLastPart = df_a($fileNameTemplateExploded, $secondFromLastPartIndex);
							df_assert_string_not_empty($secondFromLastPart);
							$fileNameTemplateExploded[$secondFromLastPartIndex] =
								implode('--', array($secondFromLastPart, '{ordering}'))
							;
							/** @var string $newFileNameTemplate */
							$newFileNameTemplate = implode('.', $fileNameTemplateExploded);
							df_assert_ne($this->getNameTemplate(), $newFileNameTemplate);
							$this->setNameTemplate($newFileNameTemplate);
						}
					}
				}
			}
			if ($this->needThrowOnReachCounterLimit()) {
				df_result_string_not_empty($result);
			}
			else {
				df_result_string($result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Zend_Date $time
	 * @param string[] $formatParts
	 * @return string
	 */
	private function formatTime(Zend_Date $time, array $formatParts) {
		return df_dts($time, implode($this->getDatePartsSeparator(), $formatParts));
	}

	/** @return int */
	private function getCounterLimit() {return $this->cfg(self::P__COUNTER_LIMIT, 100);}

	/** @return bool */
	private function getDatePartsSeparator() {return $this->cfg(self::P__DATE_PARTS_SEPARATOR, '-');}

	/** @return string */
	private function getDirectory() {return $this->cfg(self::P__DIRECTORY);}
	/** @return string */
	private function getNameTemplate() {return $this->cfg(self::P__NAME_TEMPLATE);}

	/** @return array(string => string) */
	private function getVariables() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Date $time */
			$time = Zend_Date::now();
			$time->setTimezone('Europe/Moscow');
			/** @var array(string => string) $result */
			$result =
				array_merge(
					array(
						'{date}' => $this->formatTime($time, array('y', 'MM', 'dd'))
						,'{time}' => $this->formatTime($time, array('HH', 'mm'))
						,'{time-full}' => $this->formatTime($time, array('HH', 'mm', 'ss'))
					)
					,$this->cfg(self::P__VARIABLES, array())
				)
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function needThrowOnReachCounterLimit() {
		return $this->cfg(self::P__THROW_ON_REACH_COUNTER_LIMIT, true);
	}

	/**
	 * @param string $nameTemplate
	 * @return Df_Core_Model_Fs_GetNotUsedFileName
	 */
	private function setNameTemplate($nameTemplate) {
		df_param_string_not_empty($nameTemplate, 0);
		$this->setData(self::P__NAME_TEMPLATE, $nameTemplate);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__COUNTER_LIMIT, self::V_INT, false)
			->_prop(self::P__DATE_PARTS_SEPARATOR, self::V_STRING)
			->_prop(self::P__DIRECTORY, self::V_STRING_NE)
			->_prop(self::P__NAME_TEMPLATE, self::V_STRING_NE)
			->_prop(self::P__VARIABLES, self::V_ARRAY, false)
			->_prop(self::P__THROW_ON_REACH_COUNTER_LIMIT, self::V_BOOL, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__COUNTER_LIMIT = 'counter_limit';
	const P__DATE_PARTS_SEPARATOR = 'date_parts_separator';
	const P__DIRECTORY = 'directory';
	const P__NAME_TEMPLATE = 'name_template';
	const P__VARIABLES = 'variables';
	const P__THROW_ON_REACH_COUNTER_LIMIT = 'throw_on_reach_counter_limit';
	/**
	 * @param string $directory
	 * @param string $nameTemplate
	 * @param array(string => string) $variables [optional]
	 * @param array(string => string) $additionalParams [optional]
	 * @return Df_Core_Model_Fs_GetNotUsedFileName
	 */
	public static function i(
		$directory, $nameTemplate, array $variables = array(), array $additionalParams = array()
	) {
		return new self(array_merge(array(
			self::P__DIRECTORY => $directory
			, self::P__NAME_TEMPLATE => $nameTemplate
			, self::P__VARIABLES => $variables
		), $additionalParams));
	}
}