<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <lnkbackup@vm01.telton.de>
 * @license GPLv3
 */
require_once __DIR__.'/MACTable.php';
class RebuildDNS {
	private $ipv6;
	private $scriptPath;
	private $config;
	private $mac;
	private $force = false;
	function __construct(array $argv, Config $config) {
		$this->scriptPath = __DIR__;
		$this->config = $config;
		$this->longest = array_fill(0, 5, 0);
		$this->mac = new MACTable($this->config->getMAC());
		$this->ipv6 = $this->determineIP($this->config->getDevice());
		echo "Adress ".$this->config->getDevice().": ".$this->ipv6.PHP_EOL;
		echo "Prefix: ".$this->getPrefix($this->ipv6).PHP_EOL;
		
	}
	
	function determineIP($device): string {
		$ipv6 = NULL;
		$ph = popen("ip -6 addr show dev ".$device, "r");
		while($line = trim(fgets($ph))) {
			$exp = explode(" ", $line);
			if($exp[0]=="inet6" && $exp[3]=="global") {
				$ipv6 = substr($exp[1], 0, strpos($exp[1], "/"));
			}
		}
		fclose($ph);
	return $ipv6;
	}
	
	static function getPrefix(string $ipv6):string {
		$exp = explode(":",$ipv6);
		foreach($exp as $key => $value) {
			$exp[$key] = str_pad($value, 4, "0", STR_PAD_LEFT);
		}
		return implode(":", array_slice($exp, 0, 4));
	}

	private function replaceFile($replaceMessage, $skipMessage, $filename, $contents) {
		$md5file = md5_file($filename);
		$md5content = md5($contents);
		if($md5content == $md5file && $this->force == FALSE) {
			echo $skipMessage.PHP_EOL;
			return;
		} else {
			echo $replaceMessage.PHP_EOL;
			echo $contents;
			file_put_contents($filename, $contents);
		}
	}
	
	function writeIPv6Hosts() {
		$file = NULL;
		for($i=0;$i<$this->mac->getEntries();$i++) {
			$entry = $this->mac->getEntry($i);
			$file .= $entry->getIPv6($this->getPrefix($this->ipv6))." ";
			$file .= str_pad($entry->getLongName(), $this->mac->getLongest(MACTable::LONG), " ")." ";
			$file .= $entry->getShortName()." ".PHP_EOL;
		}
		$this->replaceFile("Writing IPv6 file", "Skipping IPv6 file", $this->config->getIPv6(), $file);
	}
	
	function writeIPv4Hosts() {
		$file = NULL;
		for($i=0;$i<$this->mac->getEntries();$i++) {
			$entry = $this->mac->getEntry($i);
			$file .= str_pad($entry->getIPv4(), $this->mac->getLongest(MACTable::IP), " ")." ";
			$file .= str_pad($entry->getLongName(), $this->mac->getLongest(MACTable::LONG), " ")." ";
			$file .= $entry->getShortName()." ".PHP_EOL;
		}
		$this->replaceFile("Writing IPv4 file", "Skipping IPv4 file", $this->config->getIPv4(), $file);
	}

	function writeMAC() {
		$file = NULL;
		for($i=0;$i<$this->mac->getEntries();$i++) {
			$entry = $this->mac->getEntry($i);
			$file .= $entry->getMAC().",".$entry->getIPv4().PHP_EOL;
		}
		$this->replaceFile("Writing MAC table", "Skipping MAC table", $this->config->getDHCP(), $file);
	}
	function run() {
		$this->writeIPv6Hosts();
		echo "\n";
		$this->writeIPv4Hosts();
		echo PHP_EOL;
		$this->writeMAC();
	}

}
