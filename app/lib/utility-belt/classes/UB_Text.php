<?php
class UB_Text
{
	public static $is_email_regex = '/^[\\w_][\w\\.\\-\'_]*@([\\w\\-]+\.)+[a-zA-Z]{2,4}$/';
	public static $contains_url_http_regex = '{\\b((https?|ftp|telnet|gopher|file|wais):[\\w/\\#~:.?+=&%@!\\-]+?(?=[\\.!\\?,:]?(?:[^\\w/\\#~:.?+=&%@!\\-]|$)))}i';
	public static $is_url_http_regex = '{^(https?|ftp|telnet|gopher|file|wais):[\\w/\\#~:.?+=&%@!\\-]+$}i';
	public static $contains_url_www_regex = '{\\b((?<!:\\/\\/|@)(www|ftp)\.[\\w/\\#~:.?+=&%@!\\-]+?(?=[.:?\\-!]?(?=[^\\w/\\#~:.?+=&%@!\\-]|$)))}i';
	public static $is_url_www_regex = '{^(www|ftp)\.[\\w/\\#~:.?+=&%@!\\-]+$}i';

	/**
	 * Filters a list of words out of
	 *
	 * @param unknown_type $words
	 * @param unknown_type $txt
	 * @return unknown
	 */
	public static function filter( $words, $txt, $case_sensitive = false )
	{
		if ( !is_array($words) ) $words = array($words);
		foreach ( $words as $word ) {
			$word = trim($word);
			$txt = ($case_sensitive) ? str_replace($word, '', $txt) : str_ireplace($word, '', $txt);
		}
		return $txt;
	}
	/**
	 * Formats a string with 10-digits into a phone number format (###) ###-###
	 *
	 * @param string $string
	 * @return string
	 */
	public static function formatPhone( $string )
	{
		$val = preg_replace("/^(\\d{3})(\\d{3})(\\d{4})$/", '($1) $2-$3', self::numbersOnly($string));
		return ($val == '') ? $string : $val;
	}
	/**
	 * Removes any non-digits from the string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function numbersOnly( $string )
	{
		return preg_replace('/[^\d]/', '', $string);
	}
	/**
	 * Returns a decimal number(s) in the string.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function decimalNumbersOnly( $string )
	{
		$string = self::numbersOnly($string);
		return preg_replace('/^(\d*\.*\d*)\.\d*/','$1', $string);
	}
	/**
	 * Cleans post values. Example for all post values:
	 *
	 * 		$clean = UB_Text::stringFromPost( array_keys($_POST) );
	 *
	 * @param mixed $params
	 * @return mixed
	 */
	public static function stringFromPost( $params, $override = null ) {
		if ( is_null($override) || !is_array($override) ) {
			$override = $_POST;
		}
		$final = array();
		$key = null;
		if (!is_array($params)) {
			$key = $params;
			$params = array($key);
		}
		foreach ($params as $param) {
			$final[$param] = self::clean($override[$param]);
		}
		return !empty($key) ? $final[$key] : $final;
	}
	/**
	 * Cleans a string from slashes
	 *
	 * @param string $txt
	 * @return string
	 */
	public static function clean( $txt )
	{
		if ( is_array($txt) ) {
			foreach ( $txt as $k => $v ) {
				$txt[$k] = self::clean($v);
			}
		}
		else $txt = get_magic_quotes_gpc() ? stripslashes($txt) : $txt;
		return $txt;
	}
	/**
	 * Returns a formatted SSN string. Can choose to hide 0-9 digits
	 * with $hideChar string.
	 *
	 * @param string $string
	 * @param int $hideNumber
	 * @param string $hideChar
	 * @return string
	 */
	public static function formatSSN( $string, $hideNumber = 0, $hideChar = '*' )
	{
		if (UB_Text::isSSN($string))
		{
			if ($hideNumber > 9) $hideNumber = 9;
			$string = substr(self::numbersOnly($string), 0, 9); // get 9 digits.
			$string = preg_replace("/^\\d{".$hideNumber. "}/", str_repeat($hideChar, $hideNumber, $string), $string);
			$string = preg_replace('/(.{3})(.{2})(.{4})/', '$1-$2-$3', $string);
			return $string;
		} else return $string;
	}
	/**
	 * Converts valid e-mail address in the string to clickable HTML links.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function emailToLinks( $string )
	{
		$expression = "/\\b([\\w_][\w\\.\\-\'_]*@([\\w\\-]+\.)+\w{2,4})(?=[\\.!\\?,]?(?=([^\\w\\-\\.\'_]|$)))/";
		return preg_replace($expression, '<a href="mailto:$1">$1</a>', $string);
	}
	/**
	 * Converts valid URL's (http(s), ftp, www). NOTE: will grab trailing periods in URLs.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function urlToLinks( $string )
	{
		$string = preg_replace(self::$contains_url_http_regex, '<a href="$1">$1</a>', $string);
		$string = preg_replace(self::$contains_url_www_regex, '<a href="http://$1">$1</a>', $string);
		return $string;
	}
	/**
	 * Wrapper for self::emailToLinks and self::urlToLinks.
	 * Converts web and email addresses to HTML links.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function textToLinks( $string )
	{
		$string = self::emailToLinks($string);
		$string = self::urlToLinks($string);
		return $string;
	}
	/**
	 * Custom XHTML and XHTML Entity escaping.
	 *
	 * @param unknown_type $text
	 * @param unknown_type $html
	 * @param unknown_type $entities
	 * @return unknown
	 */
	public static function escape($text, $html = true, $entities = true) {
		if ($html) $text = htmlentities($text);
		else {
			// fix any non-terminated line breaks
			$text = str_replace('<br>', '<br />', $text);
		}
		if ($entities) {
			$entities = array(
				'–' => 'ndash',
				'—' => 'mdash',
				'™' => 'trade',
				'©' => 'copy',
				'®' => 'reg',
			);
			foreach ($entities as $char => $entity) {
				$text = str_replace($char, "&{$entity};", $text);
			}
		}
		return $text;
	}
	/**
	 * Validates an e-mail address.
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function isEmail( $string )
	{
		return (bool) preg_match(self::$is_email_regex, $string);
	}
	/**
	 * Checks to see if URL send is valid.
	 *
	 * @param unknown_type $string
	 * @return unknown
	 */
	public static function isUrl( $string )
	{
		// valid chars: 0-9 a-Z . ~ - # / _ : @ %
		$http = (bool) preg_match(self::$is_url_http_regex, $string);
		$www = (bool) preg_match(self::$is_url_www_regex, $string);
		return $http || $www;
	}
	/**
	 * Validates a 9 digit Social Security Number in numeric
	 * or ###-##-#### format.
	 *
	 * Send $numbersOnly as true to convert to numbers only first.
	 *
	 * @param string $string
	 * @param boolean $numbersOnly
	 * @return bool
	 */
	public static function isSSN( $string, $numbersOnly = false )
	{
		if ($numbersOnly) $string = UB_Text::numbersOnly($string);
		return (bool) preg_match('/^\\d{3}\\-?\\d{2}\\-?\\d{4}$/', $string);
	}
	/**
	 * Checks to see if value passed is a valid MySQL 14-digit timestamp
	 * regardless of format.
	 *
	 * @param string $ts
	 * @return boolean
	 */
	public static function isTimestamp( $string )
	{
		$ts = UB_Text::numbersOnly($string);
		return strlen($string) == 14;
	}
	/**
	 * Validates a 5-digit / 9-digit zip code.
	 *
	 * @param string $zip
	 * @return bool
	 */
	public static function isZipCode( $zip ){
		return (bool) preg_match('/^\\d{5}(\\-\\d{4})?$/', $zip);
	}
	/**
	 * Validates 10-digit phone number
	 *
	 * @param string $number
	 * @param bool $ignoreChars
	 * @return bool
	 */
	public static function isPhone( $number, $ignoreChars = true ) {
		$phone = $ignoreChars ? UB_Text::numbersOnly($number) : $number;
		return (bool) preg_match('/^\\d{10}$/', $phone);
	}
	public static function containsURL( $text ) {
		return preg_match(self::$contains_url_http_regex, $text) || preg_match(self::$contains_url_www_regex, $text);
	}
	/**
	 * Converts a string to Camel Case. Remember to quote delimiters where needed
	 * especially if you are not using space, dash, or underscore.
	 *
	 * @param string $string
	 * @param string $delimiter
	 * @return string
	 */
	public static function toCamelCase( $string, $delimiter = '\\s' )
	{
		if ( $delimiter == '-' || $delimiter == '_' || $delimiter == ' ' ) {
			$delimiter = '\\' . $delimiter;
		}
		$camelCase = strtolower($string);
		$camelCase = preg_replace('/(' . $delimiter . '[a-z])/', strtoupper('\\1'), $string);
		return $camelCase;
	}
	/**
	 * Converts a camelCase string back to a normal string using a custom delimiter.
	 * Default delimiter is a space.
	 *
	 * @param string $string
	 * @param string $delimiter
	 * @return string
	 */
	public static function fromCamelCase( $string, $delimiter = ' ' )
	{
		return trim(preg_replace('/([A-Z])/', $delimiter . strtolower('\\1'), $string));
	}
}
?>