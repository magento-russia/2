<?php
namespace Df\YandexMarket\Category\Advisor;
class CaseT extends \Df_Core_Model {
	/** @return string[] */
	public function getSuggestions() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result  */
			$result = [];
			foreach (\Df\YandexMarket\Categories::s()->getNodesAsTextArray() as $path) {
				/** @var string $path */
				if (df_contains($path, $this[self::$P__PIECE])) {
					$result[]= $path;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PIECE, DF_V_STRING_NE);
	}

	/** @var string */
	private static $P__PIECE = 'piece';
	/**
	 * @static
	 * @param string $piece
	 * @return self
	 */
	public static function i($piece) {return new self([self::$P__PIECE => $piece]);}
}

