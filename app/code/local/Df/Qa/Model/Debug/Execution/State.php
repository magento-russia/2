<?php
class Df_Qa_Model_Debug_Execution_State extends Df_Core_Model_Abstract {
	/**
	 * @override
	 * @return string
	 */
	public function __toString() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Метод __toString() не имеет права возбуждать исключительных ситуаций.
			 * Fatal error: Method __toString() must not throw an exception
			 * @link http://stackoverflow.com/questions/2429642/why-its-impossible-to-throw-exception-from-tostring
			 */
			try {
				/** @var string[][] $templateParams */
				$templateParams = array(
					array('Файл', '%file%')
					,array('Строка', '%line%')
					,array('Субъект', '%who%')
					,array('Объект', '%what%')
				);
				if ($this->showCodeContext() && $this->getCodeContext()) {
					$templateParams[]= array('Контекст', "\n%context%\n" . str_repeat('*', 36));
				}
				/** @var string $template */
				$template =
					implode(
						Df_Core_Const::T_NEW_LINE
						,array_map(array($this, 'implodeParam'), $templateParams)
					)
				;
				/** @var string $result */
				$result = strtr($template, array(
					'%file%' => str_replace(BP . DS, '', $this->getFilePath())
					,'%line%' => $this->getLine()
					,'%class%' => $this->getClassName()
					,'%function%' => $this->getFunctionName()
					,'%who%' => $this->getWho()
					,'%what%' => $this->getWhat()
					,'%context%' => $this->getCodeContext()
				));
				$this->{__METHOD__} = $result;
			}
			catch(Exception $e) {
				$this->{__METHOD__} = rm_ets($e);
			}
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getClassName() {return $this->cfg(self::P__CLASS, '');}

	/** @return string */
	public function getCodeContext() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			if (
					is_file($this->getFilePath())
				&&
					(0 < $this->getLine())
			) {
				/** @var string[] $fileContents */
				$fileContents = file($this->getFilePath());
				if (is_array($fileContents)) {
					$result =
						df_trim(
							implode(
								/**
								 * Перенос строки здесь не нужен,
								 * потому что строки с кодом
								 * уже содержат переносы на следующую стоку
								 * @link http://php.net/manual/en/function.file.php
								 */
								array_slice(
									$fileContents
									,max(0, $this->getLine() - self::CONTEXT_RADIUS)
									,2 * self::CONTEXT_RADIUS
								)
							)
							,$charlist = "\r\n"
						)
					;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFilePath() {return $this->cfg(self::P__FILE, '');}

	/** @return string */
	public function getFunctionName() {return $this->cfg(self::P__FUNCTION, '');}

	/** @return int */
	public function getLine() {return $this->cfg(self::P__LINE, 0);}

	/** @return ReflectionMethod|null */
	public function getMethod() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				($this->getClassName() && $this->getFunctionName())
				? new ReflectionMethod($this->getClassName(), $this->getFunctionName())
				: null
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getMethodName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_concat_clean('::', $this->getClassName(), $this->getFunctionName());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int $paramOrdering  		порядковый номер параметра метода
	 * @return ReflectionParameter
	 */
	public function getMethodParameter($paramOrdering) {
		df_param_integer($paramOrdering, 0);
		if (!isset($this->{__METHOD__}[$paramOrdering])) {
			// Метод должен существовать
			df_assert(is_object($this->getMethod()));
			// Параметр должен существовать
			if ($paramOrdering >= count($this->getMethod()->getParameters())) {
				/**
				 * Использовать надо именно @see df_error_internal, а не @see  df_error,
				 * чтобы сбой модуля доставки на экране оформления заказа был записан в журнал сбоев.
				 */
				df_error_internal(
					'Программист ошибочно пытается получить значение параметра с индексом %d метода «%s»,'
					. ' хотя этот метод принимает всего %d параметров.'
					, $paramOrdering
					, $this->getMethod()->class . '::' . $this->getMethod()->name
					, count($this->getMethod()->getParameters())
				);
			}
			df_assert_lt(count($this->getMethod()->getParameters()), $paramOrdering);
			/** @var ReflectionParameter $result */
			$result = df_a($this->getMethod()->getParameters(), $paramOrdering);
			df_assert($result instanceof ReflectionParameter);
			$this->{__METHOD__}[$paramOrdering] = $result;
		}
		return $this->{__METHOD__}[$paramOrdering];
	}

	/** @return string */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_sprintf('%s «%s»', $this->isItMethod() ? 'Метод' : 'Функция', $this->getMethodName())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getNameLc() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_text()->lcfirst($this->getName());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isItMethod() {return !!$this->getClassName();}

	/** @return Df_Qa_Model_Debug_Execution_State */
	private function getStateNext() {return $this->cfg(self::P__STATE_NEXT);}

	/** @return Df_Qa_Model_Debug_Execution_State */
	private function getStatePrevious() {return $this->cfg(self::P__STATE_PREVIOUS);}

	/** @return string */
	private function getWhat() {
		return rm_concat_clean('::', $this->getClassName(), $this->getFunctionName());
	}

	/** @return string */
	private function getWho() {
		return
			!$this->getStateNext()
			? ''
			: rm_concat_clean('::'
				,$this->getStateNext()->getClassName()
				,$this->getStateNext()->getFunctionName()
			)
		;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param array $param
	 * @return string
	 */
	private function implodeParam(array $param) {
		/** @var int $labelColumnWidth */
		$labelColumnWidth = 12;
		/** @var string $label */
		$label = df_a($param, 0);
		$value = df_a($param, 1);
		/** @var int $labelLength */
		$labelLength = mb_strlen($label);
		/** @var string $result */
		$result =
			rm_sprintf(
				'%s:%s%s'
				,$label
				,str_pad(' ', $labelColumnWidth - $labelLength)
				,$value
			)
		;
		return $result;
	}

	/** @return bool */
	private function showCodeContext() {return $this->cfg(self::P__SHOW_CODE_CONTEXT, true);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CLASS, self::V_STRING, false)
			->_prop(self::P__FILE, self::V_STRING, false)
			->_prop(self::P__FUNCTION, self::V_STRING, false)
			// Тут должен стоять именно валидатор Df_Zf_Validate_String,
			// потому что в стеке номера строк хранятся почему-то как строки, а не как числа
			->_prop(self::P__LINE, self::V_INT, false)
			->_prop(self::P__SHOW_CODE_CONTEXT, self::V_BOOL, false)
			->_prop(self::P__STATE_NEXT, __CLASS__, false)
			->_prop(self::P__STATE_PREVIOUS, __CLASS__, false)
		;
	}
	const _CLASS = __CLASS__;
	const CONTEXT_RADIUS = 8;
	const P__CLASS = 'class';
	const P__FILE = 'file';
	const P__FUNCTION = 'function';
	const P__LINE = 'line';
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
	const P__STATE_NEXT = 'state_next';
	const P__STATE_PREVIOUS = 'state_previous';
	const REFLECTION_METHOD_CLASS = 'ReflectionMethod';
	/**
	 * @param array $parameters
	 * @return Df_Qa_Model_Debug_Execution_State
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}