<?php
class Df_YandexMarket_Model_Category_Adviser_Case extends Df_Core_Model {
	/** @return string[] */
	public function getSuggestions() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result  */
			$result = array();
			foreach (Df_YandexMarket_Model_Categories::s()->getNodesAsTextArray() as $path) {
				/** @var string $path */
				if (df_contains($path, $this->getPiece())) {
					$result[]= $path;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPiece() {return $this->cfg(self::P__PIECE);}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PIECE, DF_V_STRING_NE);
	}
	
	const P__PIECE = 'piece';
	/**
	 * @static
	 * @param string $piece
	 * @return Df_YandexMarket_Model_Category_Adviser_Case
	 */
	public static function i($piece) {return new self(array(self::P__PIECE => $piece));}
}

