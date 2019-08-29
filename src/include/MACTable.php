<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <rebuild-dns@vm01.telton.de>
 * @license GPLv3
 */
class MACTable {
	private $entries;
	private $longest = array();
	const MAC = 0;
	const IP = 1;
	const LONG = 2;
	const SHORT = 3;
	function __construct($table) {
		$this->longest = array_fill(0, self::SHORT+1, 0);
		$handle = fopen($table, "r");
		while($line = fgets($handle)) {
			$trimmed = trim($line);
			if($trimmed==NULL) {
				continue;
			}
			$split = preg_split("/\s+/", $trimmed);
			for($i=0;$i<=self::SHORT;$i++) {
				$this->writeLongest($i, $split[$i]);
			}
			$entry = new MACEntry($split);
			$this->entries[] = $entry;
		}
		fclose($handle);
	}
	
	private function writeLongest(int $i, string $value) {
		$len = strlen($value);
		if($this->longest[$i]<$len) {
			$this->longest[$i] = $len;
		}
	}
	
	function getEntries(): int {
		return count($this->entries);
	}
	
	function getEntry(int $i): MACEntry {
		return $this->entries[$i];
	}
	
	function getLongest(int $i):int {
		return $this->longest[$i];
	}
}