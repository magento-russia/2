<?php
namespace Df\C1\Cml2\Import\Data\Collection\ProductPart;
use Df\C1\Cml2\Import\Data\Entity\ProductPart\Image;
class Images extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @uses unlink()
	 * @return void
	 */
	public function deleteFiles() {@array_map('unlink', $this->getFullPaths());}

	/** @return string[] */
	public function getFullPaths() {return dfc($this, function() {return
		/** @uses \Df\C1\Cml2\Import\Data\Entity\ProductPart\Image::getFilePathFull() */
		df_each($this, 'getFilePathFull')
	;});}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Image::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'Картинка';}

	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\ProductЖЖgetImages()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return self
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}