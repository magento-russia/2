<?php
class Df_Core_Block_Element_Image extends Df_Core_Block_Element {
	/** @return string */
	public function getAlt() {return $this->cfg(self::P__ALT, '');}
	/** @return string */
	public function getTitle() {return $this->cfg(self::P__TITLE, '');}
	/** @return string */
	public function getSrc() {return $this->cfg(self::P__SRC, '');}

	/**
	 * Если данный метод вернёт true, то система не будет рисовать данный блок.
	 * @override
	 * @return bool
	 */
	protected function isBlockEmpty() {return !$this->getSrc();}

	const _CLASS = __CLASS__;
	const P__ALT = 'alt';
	const P__SRC = 'src';
	const P__TITLE = 'title';
}