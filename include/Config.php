<?php
class Config {
	private $parsed;
	function __construct($filename) {
		if(!file_exists($filename)) {
			throw new Exception("configuration file ".$filename." not found.");
		}
		$this->parsed = parse_ini_file($filename);
		$mandatory = array("ipv4", "ipv6", "dhcp", "mac", "device");
		foreach($mandatory as $value) {
			if(empty($this->parsed[$value])) {
				throw new Exception("ini value '".$value."' is not set.");
			}
		}
	}
	
	function getParsed(): array {
		return $this->parsed;
	}
}