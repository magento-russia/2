<?php
class Df_Localization_Realtime_Dictionary_ModulePart_Block extends \Df\Xml\Parser\Entity {
	/** @return string|null */
	public function getBlockClass() {return $this->getAttribute('class');}

	/**
	 * @override
	 * @see Df_Core_Model::getId()
	 * @return string
	 */
	public function getId() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getName();
			if (!$result) {
				$result = "{$this->getBlockClass()}::{$this->getTemplate()}";
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::getName()
	 * @return string
	 */
	public function getName() {return df_nts($this->getAttribute('name'));}

	/**
	 * @param string $template
	 * @return bool
	 */
	public function matchTemplate($template) {
		$template = df_path_n($template);
		return
			$this->isTemplateMatchesAll()
			||
				(
					 // Поддержка синтаксиса
					 // template='tierprices.phtml$'
					 // Использование такого синтаксиса должно дать прирост производительности
					 // по сравнению с аналогичным регулярным выражением
					 // template='#tierprices\.phtml$#'
					$this->isTemplateEnd() && df_ends_with($template, $this->getTemplateEnd())
					// 2015-10-16
					// Поддержка синтаксиса template='*ajaxcart*'
					||
						$this->isTemplateWildcard()
						&& df_contains($template, $this->getTemplateWildcardBody())
					|| $this->isTemplateRegex() && df_preg_test($this->getTemplate(), $template)
					|| ($this->getTemplate() === $template)
				)
		;
	}

	/** @return Df_Localization_Realtime_Dictionary_ModulePart_Terms */
	public function terms() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Localization_Realtime_Dictionary_ModulePart_Terms::i($this->e());
		}
		return $this->{__METHOD__};
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

	/**
	 * 2015-10-16
	 * Поддержка синтаксиса template='*ajaxcart*'
	 * @return string
	 */
	private function getTemplateWildcardBody() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trim($this->getTemplate(), '*');
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
			$this->{__METHOD__} = df_ends_with($this->getTemplate(), '$');
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	private function isTemplateRegex() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_t()->isRegex($this->getTemplate());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-10-16
	 * Поддержка синтаксиса template='*ajaxcart*'
	 * @return bool
	 */
	private function isTemplateWildcard() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_starts_with($this->getTemplate(), '*')
				&& df_ends_with($this->getTemplate(), '*')
			;
		}
		return $this->{__METHOD__};
	}

	/** @used-by Df_Localization_Realtime_Dictionary_ModulePart_Blocks::itemClass() */

}