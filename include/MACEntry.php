<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <lnkbackup@vm01.telton.de>
 * @license GPLv3
 */
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
	
	public function getMAC():string {
		return $this->mac;
	}
	
	public function getIPv4():string {
		return $this->ip;
	}

	public function getLongName():string {
		return $this->longName;
	}
	
	public function getShortName():string {
		return $this->shortName;
	}
}