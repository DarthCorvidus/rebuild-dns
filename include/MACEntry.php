<?php
class MACEntry {
	private $mac;
	private $ip;
	private $longName;
	private $shortName;
	function __construct(array $array) {
		$this->mac = strtolower($array[0]);
		$this->ip = $array[1];
		$this->longName = $array[2];
		$this->shortName = $array[3];
	}
}