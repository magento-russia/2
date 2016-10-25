<?php
namespace Df\Ems\Api\Locations;
class Regions extends \Df\Ems\Api\Locations {
	/** @return array(int => string) */
	public function mapToEmsIdFromMagentoId() {return dfc($this, function($m) {return
		df_cache_get_simple($m, function() {return
			array_flip(array_map(
				// Не переведутся: 'КАЗАХСТАН', 'ТАЙМЫРСКИЙ АО', 'ТАЙМЫРСКИЙ ДОЛГАНО-НЕНЕЦКИЙ РАЙОН'
				function($nameEms) {return
					df_region_id_by_name_ru(dftr($nameEms, [
						'СЕВЕРНАЯ ОСЕТИЯ-АЛАНИЯ РЕСПУБЛИКА' => 'СЕВЕРНАЯ ОСЕТИЯ — АЛАНИЯ РЕСПУБЛИКА'
						,'ТЫВА РЕСПУБЛИКА' => 'ТЫВА (ТУВА) РЕСПУБЛИКА'
						,'ХАНТЫ-МАНСИЙСКИЙ-ЮГРА АВТОНОМНЫЙ ОКРУГ' => 'ХАНТЫ-МАНСИЙСКИЙ АВТОНОМНЫЙ ОКРУГ'
					]))
				;}
				,array_column($this->locationsRaw(), 'name', 'value')
			))
		;})
	;}, [__METHOD__]);}
}