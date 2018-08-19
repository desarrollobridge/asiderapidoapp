<?php
class MobileUnicode
{
	
	public static function jsonUnicode1($arr)
	{
		//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
		array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
		return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
	}
		
	public static function jsonUnicode2($data='')
	{
		$data = preg_replace_callback(
	    '/\\\\u([0-9a-f]{4})/i', function ($matches) {
	        $sym = mb_convert_encoding(
	            pack('H*', $matches[1]),
	            'UTF-8',
	            'UTF-16'
	        );
	        return $sym;
	    },
	    $data
	    );
	}
		
}