<?php
class Df_Core_Model_Url_Rewrite extends Mage_Core_Model_Url_Rewrite {
	/**
	 * Цель перекрытия —
	 * поддержка русских букв в адресах страниц.
	 * @override
	 * @param string $path
	 * @return Mage_Core_Model_Url_Rewrite
	 */
	public function loadByRequestPath($path) {
		/** @var bool */
 		static $patchEnabled;
		if (is_null($patchEnabled)) {
			$patchEnabled = df_cfg()->seo()->urls()->getPreserveCyrillic();
		}
		return parent::loadByRequestPath(
			!$patchEnabled
			? $path
			: (!is_array($path) ? rawurldecode($path) : array_map('rawurldecode', $path))
		);
	}

	const _C = __CLASS__;
	const P__ID = 'url_rewrite_id';
	const P__ID_PATH = 'id_path';
	const P__IS_SYSTEM = 'is_system';
	const P__OPTIONS = 'options';
	const P__REQUEST_PATH = 'request_path';
	const P__STORE_ID = 'store_id';
	const P__TARGET_PATH = 'target_path';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Url_Rewrite
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Core_Model_Url_Rewrite
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}