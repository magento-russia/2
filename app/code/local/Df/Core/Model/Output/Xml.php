<?php
class Df_Core_Model_Output_Xml extends Df_Core_Model_Abstract {
	/**
	 * @param string $text
	 * @return string
	 */
	public function mark($text) {return self::$TAG__BEGIN . $text . self::$TAG__END;}

	/**
	 * @param string $text
	 * @return string
	 */
	public function outputPlain($text) {
		return strtr($text, array(self::$TAG__BEGIN => '', self::$TAG__END => ''));
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function outputHtml($text) {
		return
			!rm_contains($text, self::$TAG__BEGIN)
			? $text
			: preg_replace_callback(
				strtr(
					'#{tag-begin}([\s\S]*){tag-end}#mui'
					,array('{tag-begin}' => self::$TAG__BEGIN, '{tag-end}' => self::$TAG__END)
				)
				, array('self', 'processOutputForHtml')
				, $text
			)
		;
	}

	/** @var string */
	private static $TAG__BEGIN = '{rm-xml}';
	/** @var string */
	private static $TAG__END = '{/rm-xml}';

	/** @return Df_Core_Model_Output_Xml */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * @param string[] $matches
	 * @return string
	 */
	private static function processOutputForHtml(array $matches) {
		return sprintf('<pre>%s</pre>', df_escape(rm_normalize(df_a($matches, 1, ''))));
	}
}