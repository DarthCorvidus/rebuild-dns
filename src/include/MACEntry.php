<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <rebuild-dns@vm01.telton.de>
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

	function getIPv6(string $prefix):string {
		$exp = explode(":", strtolower($this->mac));
		$first = str_pad(decbin(hexdec($exp[0])), 8, 0, STR_PAD_LEFT);
		$first[6] = ($first[6]==1?0:1);
		$exp[0] = str_pad(dechex(bindec($first)), 2, 0, STR_PAD_LEFT);
		$final = array();
		foreach($exp as $key => $value) {
			if($key==3) {
				$final[] = "ff";
				$final[] = "fe";
			}
			$final[] = $value;
		}
		for($i=0;$i<count($final);$i = $i+2) {
			$ipv6[] = $final[$i].$final[$i+1];
		}
	return $prefix.":".implode(":", $ipv6);
	}
	
	public function getLongName():string {
		return $this->longName;
	}
	
	public function getShortName():string {
		return $this->shortName;
	}
}