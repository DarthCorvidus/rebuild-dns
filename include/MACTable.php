<?php
require_once __DIR__.'/MACEntry.php';
class MACTable {
	private $entries;
	function __construct($table) {
		$handle = fopen($table, "r");
		while($line = fgets($handle)) {
			$trimmed = trim($line);
			if($trimmed==NULL) {
				continue;
			}
			$split = preg_split("/\s+/", $trimmed);
			$entry = new MACEntry($split);
			$this->entries[] = $entry;
		}
		fclose($handle);
	}
	
	function getEntries(): int {
		return count($this->entries);
	}
	
	function getEntry(int $i): MACEntry {
		return $this->entries[$i];
	}
}