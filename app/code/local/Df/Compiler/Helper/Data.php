<?php
class Df_Compiler_Helper_Data extends Mage_Compiler_Helper_Data {
	/** @return Df_Compiler_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}