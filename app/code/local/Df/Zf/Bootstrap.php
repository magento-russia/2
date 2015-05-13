<?php
/**
 * Удалять этот класс нельзя,
 * потому что метод @see Df_Zf_Bootstrap::init()
 * вызывается из config.xml
 */
class Df_Zf_Bootstrap extends Df_Core_Bootstrap_Abstract {
	/** @return void */
	public function init() {Df_Zf_LibLoader::s();}

	/** @return Df_Zf_Bootstrap */
	public static function s() {
		/**
		 * Используем именно @see Mage::getSingleton(),
		 * чтобы вызов @see Df_Zf_Bootstrap::s()
		 * возвращал тот же объект,
		 * который используется для обработки событий системным диспетчером:
				<observers>
					<df_zf_controller_front_init_before>
						<class>Df_Zf_Bootstrap</class>
						<method>init</method>
					</df_zf_controller_front_init_before>
				</observers>
		 		(...)
				<observers>
					<df_zf_default>
						<class>Df_Zf_Bootstrap</class>
						<method>init</method>
					</df_zf_default>
				</observers>
		 		(...)
				<observers>
					<df_zf_resource_get_tablename>
						<class>Df_Zf_Bootstrap</class>
						<method>resource_get_tablename</method>
					</df_zf_resource_get_tablename>
				</observers>
		 */
		return Mage::getSingleton(__CLASS__);
	}
}