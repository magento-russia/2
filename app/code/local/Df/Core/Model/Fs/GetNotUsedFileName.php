<?php
class Df_Core_Model_Fs_GetNotUsedFileName extends Df_Core_Model {
	/**
	 * Результатом всегда является непустая строка.
	 * @used-by r()
	 * @return string
	 * @throws Exception
	 */
	private function _result() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			/** @var int $counter */
			$counter = 1;
			/** @var bool $hasOrderingPosition */
			$hasOrderingPosition = df_contains($this->getTemplate(), '{ordering}');
			while (true) {
				/** @var string $fileName */
				$fileName = strtr($this->getTemplate(), array_merge(
					array('{ordering}' => sprintf('%03d', $counter))
					, $this->getVariables()
				));
				/** @var string $fileFullPath */
				$fileFullPath = $this->getDirectory() . DS . $fileName;
				if (!file_exists($fileFullPath)) {
					/**
					 * Раньше здесь стояло file_put_contents,
					 * и иногда почему-то возникал сбой:
					 * failed to open stream: No such file or directory.
					 * Может быть, такой сбой возникает, если папка не существует?
					 */
					$result = $fileFullPath;
					break;
				}
				else {
					if ($counter > 100) {
						df_error('Счётчик достиг предела (%d).', $counter);
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
							$fileNameTemplateExploded = explode('.', $this->getTemplate());
							/** @var int $secondFromLastPartIndex*/
							$secondFromLastPartIndex =  max(0, count($fileNameTemplateExploded) - 2);
							/** @var string $secondFromLastPart */
							$secondFromLastPart = dfa($fileNameTemplateExploded, $secondFromLastPartIndex);
							df_assert_string_not_empty($secondFromLastPart);
							$fileNameTemplateExploded[$secondFromLastPartIndex] =
								implode('--', array($secondFromLastPart, '{ordering}'))
							;
							/** @var string $newFileNameTemplate */
							$newFileNameTemplate = implode('.', $fileNameTemplateExploded);
							df_assert_ne($this->getTemplate(), $newFileNameTemplate);
							$this[self::$P__TEMPLATE] = $newFileNameTemplate;
						}
					}
				}
			}
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDatePartsSeparator() {return $this->cfg(self::$P__DATE_PARTS_SEPARATOR, '-');}

	/** @return string */
	private function getDirectory() {return $this[self::$P__DIRECTORY];}

	/** @return string */
	private function getTemplate() {return $this[self::$P__TEMPLATE];}

	/** @return array(string => string) */
	private function getVariables() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'{date}' => $this->nowS('y', 'MM', 'dd')
				,'{time}' => $this->nowS('HH', 'mm')
				,'{time-full}' => $this->nowS('HH', 'mm', 'ss')
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by nowS()
	 * @return Zend_Date
	 */
	private function now() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Date $result */
			$result = Zend_Date::now();
			$result->setTimezone('Europe/Moscow');
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getVariables()
	 * @return string
	 */
	private function nowS() {
		/** @var string[] $parts */
		$parts = func_get_args();
		return df_dts($this->now(), implode($this->getDatePartsSeparator(), $parts));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__DATE_PARTS_SEPARATOR, RM_V_STRING)
			->_prop(self::$P__DIRECTORY, RM_V_STRING_NE)
			->_prop(self::$P__TEMPLATE, RM_V_STRING_NE)
		;
	}
	/** @var string */
	private static $P__DATE_PARTS_SEPARATOR = 'date_parts_separator';
	/** @var string */
	private static $P__DIRECTORY = 'directory';
	/** @var string */
	private static $P__TEMPLATE = 'name_template';

	/**
	 * Результатом всегда является непустая строка.
	 * @used-by df_file_name()
	 * @param string $directory
	 * @param string $template
	 * @param string $datePartsSeparator [optional]
	 * @return string
	 */
	public static function r($directory, $template, $datePartsSeparator = '-') {
		/** @var Df_Core_Model_Fs_GetNotUsedFileName $i */
		$i = new self(array(
			self::$P__DIRECTORY => $directory
			, self::$P__TEMPLATE => $template
			, self::$P__DATE_PARTS_SEPARATOR => $datePartsSeparator
		));
		return $i->_result();
	}
}