<?php
/**
 * @singleton
 * Система создаёт объект-одиночку для потомков этого класса.
 * Не забывайте об этом при реализации кеширования результатов вычислений внутри этого класса!
 */
class Df_Admin_Config_Source_LetterCase extends Df_Admin_Config_Source {
	/**
	 * @override
	 * @param bool $isMultiSelect
	 * @return array(array(string => string))
	 */
	protected function toOptionArrayInternal($isMultiSelect = false) {
		return df_map_to_options(array(
			self::_DEFAULT => 'не менять'
			,self::$UCFIRST => 'с заглавной буквы'
			,self::$UPPERCASE => 'заглавными буквами'
			,self::$LOWERCASE => 'строчными буквами'
		));
	}

	/**
	 * @used-by convertToCss()
	 * @used-by isDefault()
	 * @used-by toOptionArrayInternal()
	 * @used-by Df_Admin_Config_Font::getLetterCase()
	 */
	const _DEFAULT = 'default';

	/**
	 * @used-by Df_Admin_Config_Font::getLetterCaseCss()
	 * @param string $value
	 * @return string
	 */
	public static function css($value) {
		return dfa(array(
			self::_DEFAULT => 'none'
			,self::$UPPERCASE => self::$UPPERCASE
			,self::$LOWERCASE => self::$LOWERCASE
			,self::$UCFIRST => 'capitalize'
		), $value);
	}

	/**
	 * @used-by Df_Admin_Config_Font::applyLetterCase()
	 * @param string $text
	 * @param string $format
	 * @return string
	 */
	public static function apply($text, $format) {
		/** @var string $result */
		switch($format) {
			case self::$LOWERCASE:
				$result = mb_strtolower($text);
				break;
			case self::$UPPERCASE:
				$result = mb_strtoupper($text);
				break;
			case self::$UCFIRST:
				/**
				 * 2016-03-23
				 * Раньше алгоритм был таким:
				 * $result = df_ucfirst(mb_strtolower(df_trim($text)));
				 * Это приводило к тому, что настроечная опция
				 * «Использовать ли HTTPS для административной части?»
				 * отображались как «Использовать ли https для административной части?».
				 * Уже не помню, зачем я ранее здесь использовал @see mb_strtolower
				 */
				$result = df_ucfirst(df_trim($text));
				break;
			default:
				$result = $text;
		}
		/**
		 * Убрал валидацию результата намеренно: сам метод безобиден,
		 * и даже если он как-то неправильно будет работать — ничего страшного.
		 * Пока метод дал сбой только один раз, в магазине laap.ru
		 * при форматировании заголовков административной таблицы товаров
		 * (видимо, сбой произошёл из-за влияния некоего стороннего модуля).
		 */
		return $result;
	}

	/**
	 * @used-by Df_Admin_Config_Font::isDefault()
	 * @return bool
	 */
	public static function isDefault($value) {return self::_DEFAULT === $value;}

	/**
	 * @used-by Df_Admin_Config_Font::isUcFirst()
	 * @return bool
	 */
	public static function isUcFirst($value) {return self::$UCFIRST === $value;}

	/** @var string */
	private static $LOWERCASE = 'lowercase';
	/** @var string */
	private static $UCFIRST = 'ucfirst';
	/** @var string */
	private static $UPPERCASE = 'uppercase';
}