<?php
/**
 * Based on the classes from Danilo Segan <danilo@kvota.net>
 */


/**
 * Provides file-like methods for manipulating a string instead
 * of a physical file.
 */
class StringReader {
  var $_pos;
  var $_str;

  function StringReader($str='') {
    $this->_str = $str;
    $this->_pos = 0;
  }

  function read($bytes) {
    $data = substr($this->_str, $this->_pos, $bytes);
    $this->_pos += $bytes;
    if (strlen($this->_str)<$this->_pos)
      $this->_pos = strlen($this->_str);

    return $data;
  }

  function seekto($pos) {
    $this->_pos = $pos;
    if (strlen($this->_str)<$this->_pos)
      $this->_pos = strlen($this->_str);
    return $this->_pos;
  }

  function pos() {
    return $this->_pos;
  }

  function length() {
    return strlen($this->_str);
  }

}

class CachedFileReader extends StringReader {
	function CachedFileReader($filename) {
		$this->_str = file_get_contents($filename);
		if (false ===$this->_str)
			return false;
		$this->pos = 0;
	}
}

/**
 * Allows reading integers from a file.
 */
class CachedIntFileReader extends CachedFileReader {

	var $endian = 'little';

	/**
	 * Opens a file and caches it.
	 *
	 * @param $filename string name of the file to be opened
	 * @param $endian string endianness of the words in the file, allowed
	 * 	values are 'little' or 'big'. Default value is 'little'
	 */
	function CachedIntFileReader($filename, $endian = 'little') {
		$this->endian = $endian;
		parent::CachedFileReader($filename);
	}

	/**
	 * Sets the endianness of the file.
	 *
	 * @param $endian string 'big' or 'little'
	 */
	function setEndian($endian) {
		$this->endian = $endian;
	}

	/**
	 * Reads a 32bit Integer from the Stream
	 *
	 * @return mixed The integer, corresponding to the next 32 bits from
	 * 	the stream of false if there are not enough bytes or on error
	 */
	function readint32() {
		$bytes = $this->read(4);
		if (4 != strlen($bytes))
			return false;
		$endian_letter = ('big' == $this->endian)? 'N' : 'V';
		$int = unpack($endian_letter, $bytes);
		return array_shift($int);
	}

	/**
	 * Reads an array of 32-bit Integers from the Stream
	 *
	 * @param integer count How many elements should be read
	 * @return mixed Array of integers or false if there isn't
	 * 	enough data or on error
	 */
	function readint32array($count) {
		$bytes = $this->read(4 * $count);
		if (4*$count != strlen($bytes))
			return false;
		$endian_letter = ('big' == $this->endian)? 'N' : 'V';
		return unpack($endian_letter.$count, $bytes);
	}
}

?>
