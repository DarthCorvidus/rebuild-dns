<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <lnkbackup@vm01.telton.de>
 * @license GPLv3
 */
require_once __DIR__.'/MACTable.php';
class RebuildDNS {
	private $ipv6;
	private $values;
	private $longest;
	private $scriptPath;
	private $config;
	private $mac;
	private $force;
	function __construct(array $argv, Config $config) {
		$this->scriptPath = __DIR__;
		$this->config = $config->getParsed();
		$this->longest = array_fill(0, 5, 0);
		$this->mac = new MACTable($this->config["mac"]);
		$this->ipv6 = $this->determineIP($this->config["device"]);
		if(file_exists($this->config["currentAddress"]) && file_get_contents($this->config["currentAddress"])==$this->ipv6 && !in_array("--force", $argv)) {
			echo "Nothing to do, use --force to rewrite addresses.".PHP_EOL;
			#die();
		}

		if(!file_exists($this->config["currentAddress"])) {
			file_put_contents($this->config["currentAddress"], $this->ipv6);
		}

		if(file_get_contents($this->config["currentAddress"])!=$this->ipv6) {
			file_put_contents($this->config["currentAddress"], $this->ipv6);
		}
		
		echo "Adress ".$this->config["device"].": ".$this->ipv6.PHP_EOL;
		echo "Prefix: ".$this->getPrefix($this->ipv6).PHP_EOL;
		
		$handle = fopen($this->config["mac"], "r");
		while($line = fgets($handle)) {
			$trimmed = trim($line);
			if($trimmed==NULL) {
				continue;
			}
			$split = preg_split("/\s+/", $trimmed);
			$split[3] = self::getPrefix($this->ipv6).":".self::mac2ip($split[0]);
			$split[4] = substr($split[2], 0, strpos($split[2], "."));
			$this->values[] = $split;
		}
		fclose($handle);
		foreach($this->values as $key => $values) {
			foreach($values as $subkey => $value) {
				if($this->longest[$subkey] < strlen($value)) {
					$this->longest[$subkey] = strlen($value);
				}
			}
		}
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

	static function mac2ip($mac) {
		$exp = explode(":", strtolower($mac));
		$first = str_pad(decbin(hexdec($exp[0])), 8, 0, STR_PAD_LEFT);
		#echo $first.PHP_EOL;
		$first[6] = ($first[6]==1?0:1);
		#echo $first.PHP_EOL;
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
	return implode(":", $ipv6);
	}
	
	private function replaceFile($replaceMessage, $skipMessage, $filename, $contents) {
		$md5file = md5_file($filename);
		$md5content = md5($contents);
		if($md5content == $md5file) {
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
		foreach($this->values as $key => $value) {
			$file .= $value[3]." ";
			$file .= str_pad($value[2], $this->longest[2], " ")." ";
			$file .= str_pad($value[4], $this->longest[4], " ").PHP_EOL;
		}
		$this->replaceFile("Writing IPv6 file", "Skipping IPv6 file", $this->config["ipv6"], $file);
	}
	
	function writeIPv4Hosts() {
		$file = NULL;
		foreach($this->values as $key => $value) {
			$file .= $value[1]." ";
			$file .= str_pad($value[2], $this->longest[2], " ")." ";
			$file .= str_pad($value[4], $this->longest[4], " ").PHP_EOL;
		}
		$this->replaceFile("Writing IPv4 file", "Skipping IPv4 file", $this->config["ipv4"], $file);
	}

	function writeMAC() {
		$file = NULL;
		for($i=0;$i<$this->mac->getEntries();$i++) {
			$entry = $this->mac->getEntry($i);
			$file .= $entry->getMAC().",".$entry->getIPv4().PHP_EOL;
		}
		$this->replaceFile("Writing MAC table", "Skipping MAC table", $this->config["dhcp"], $file);
	}
	function run() {
		$this->writeIPv6Hosts();
		echo "\n";
		$this->writeIPv4Hosts();
		echo PHP_EOL;
		$this->writeMAC();
	}

}