<?php
class Df_Localization_Model_Realtime_Dictionary_ModulePart_Block
	extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string|null */
	public function getBlockClass() {return $this->getAttribute('class');}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {
		return
			$this->getName()
			? $this->getName()
			: implode('::', array($this->getBlockClass(), $this->getTemplate()))
		;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getName() {return df_nts($this->getAttribute('name'));}
	
	/** @return Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms */
	public function getTerms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Localization_Model_Realtime_Dictionary_ModulePart_Terms::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $currentBlockClass
	 * @return bool
	 */
	public function matchClass($currentBlockClass) {
		/** @var string|null $expectedClass */
		$expectedClass = $this->getBlockClass();
		return
				!$currentBlockClass
			||
				!$expectedClass
			||
				('*' === $expectedClass)
			||
				($expectedClass === $currentBlockClass)
			||
				(
						@class_exists($expectedClass)
					&&
						@class_exists($currentBlockClass)
					&&
						/**
						 * Обратите внимание, что @see is_subclass_of()
						 * вернёт false, когда классы $expectedClass и $currentBlockClass совпадают,
						 * однако для этого обработки случая у нас есть отдельное условие выше.
						 */
						is_subclass_of($currentBlockClass, $expectedClass)
				)
		;
	}

	/**
	 * @param string $template
	 * @return bool
	 */
	public function matchTemplate($template) {
		$template = df_path()->normalizeSlashes($template);
		return
				$this->isTemplateMatchesAll()
			||
				(
						 // Поддержка синтаксиса
						 // template='tierprices.phtml$'
						 // Использование такого синтаксиса должно дать прирост производительности
						 // по сравнению с аналогичным регулярным выражением
						 // template='#tierprices\.phtml$#'
						$this->isTemplateEnd() && rm_ends_with($template, $this->getTemplateEnd())
					||
						$this->isTemplateRegex() && rm_preg_test($this->getTemplate(), $template)
					||
						($this->getTemplate() === $template)
				)
		;
	}

	/** @return string|null */
	private function getTemplate() {return $this->getAttribute('template');}

	/**
	 * Поддержка синтаксиса
	 * template='tierprices.phtml$'
	 * Использование такого синтаксиса должно дать прирост производительности
	 * по сравнению с аналогичным регулярным выражением
	 * template='#tierprices\.phtml$#'
	 * @return string
	 */
	private function getTemplateEnd() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trim_right($this->getTemplate(), '$');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isTemplateMatchesAll() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !$this->getTemplate() || ('*' === $this->getTemplate());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Поддержка синтаксиса
	 * template='tierprices.phtml$'
	 * Использование такого синтаксиса должно дать прирост производительности
	 * по сравнению с аналогичным регулярным выражением
	 * template='#tierprices\.phtml$#'
	 * @return bool
	 */
	private function isTemplateEnd() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_ends_with($this->getTemplate(), '$');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isTemplateRegex() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_text()->isRegex($this->getTemplate());
		}
		return $this->{__METHOD__};
	}

	/** Используется из @see Df_Localization_Model_Realtime_Dictionary_ModulePart_Blocks::getItemClass() */
	const _CLASS = __CLASS__;
}