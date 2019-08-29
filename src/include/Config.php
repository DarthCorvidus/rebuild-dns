<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <rebuild-dns@vm01.telton.de>
 * @license GPLv3
 */
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
	
	function getIPv4(): string {
		return $this->parsed["ipv4"];
	}
	
	function getIPv6(): string {
		return $this->parsed["ipv6"];
	}

	function getDHCP(): string {
		return $this->parsed["dhcp"];
	}

	function getMAC(): string {
		return $this->parsed["mac"];
	}

	function getDevice(): string {
		return $this->parsed["device"];
	}
	
}