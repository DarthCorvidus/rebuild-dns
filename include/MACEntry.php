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
		$this->mac = strtolower($array[MACTable::MAC]);
		$this->ip = $array[MACTable::IP];
		$this->longName = $array[MACTable::LONG];
		$this->shortName = $array[MACTable::SHORT];
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