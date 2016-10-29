<?php
// 2016-10-28
namespace Df\Dellin;
/**
 * 2016-10-28
 * Пример запроса: https://www.dellin.ru/api/cities/search.json?q=Чита
 * Запрос можно подавать в любом регистре, например:
 * https://www.dellin.ru/api/cities/search.json?q=ЧИТА
 * Пример ответа:
	[
		{
			"cityID": 192906,
			"city": "Чита",
			"fullName": "Чита г (Забайкальский край)",
			"code": "7500000100000000000000000",
			"street": "1",
			"isTerminal": "1",
			"uString": "",
			"label": "Чита г (Забайкальский край)",
			"value": "Чита",
			"cashlessOnly": "0",
			"inPrice": "1",
			"isAutoCity": "1",
			"noSendDoor": "0",
			"nameString": "Чита г",
			"regionString": "Забайкальский край"
		},
		{
			"cityID": 193087,
			"city": "Чита-Забайкальск",
			"fullName": "Чита-Забайкальск автодорога (Забайкальский край)",
			"code": "7500100008600000000000000",
			<...>
		},
		<...>
	]
 */
class Locator {
	/**
	 * 2016-10-28
	 * @param string $city
	 * @param string $region
	 * @return int|null
	 */
	public static function find($city, $region) {return
		df_cache_get_simple([$city, $region], function($city, $region) {return
			df_find(function(array $l) use ($city, $region) {return
				$city === mb_strtoupper(dfa($l, 'city'))
				&& $region === mb_strtoupper(self::normalizeRegion(dfa($l, 'regionString')))
				? intval($l['cityID'])
				: false
			;}, df_http_json('https://www.dellin.ru/api/cities/search.json', ['q' => $city]))
		;}, mb_strtoupper($city), mb_strtoupper($region))
	;}

	/**
	 * 2016-10-28
	 * @param string $r
	 * @return string
	 */
	private static function normalizeRegion($r) {return
		strtr(df_trim(df_trim_text($r, ['АО', 'край', 'обл.', 'Респ.', 'г.'])), [
			'Саха /Якутия/' => 'Саха (Якутия)'
			,'Северная Осетия - Алания' => 'Северная Осетия — Алания'
			,'Тыва' => 'Тыва (Тува)'
		])
	;}
}