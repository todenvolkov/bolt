<?
class CSVTools {
	var $files = array(); // handle file
	var $conf = array(); 
	
	public function CSVTools() {
		$this->configure();
	}
	
	public function configure($array=''){
		//$this->debug('configure');
		//$this->debug($array);
		//$this->conf['lenghtString'] = 1000;
		$array['lenghtString']=='' ? $this->conf['lenghtString'] = 1000 : $this->conf['lenghtString'] = $array['lenghtString'];
		$array['delimiter'] == '' ? $this->conf['delimiter'] = '|' : $this->conf['delimiter'] = $array['delimiter'];
		$array['stringOff'] == '' ? $this->conf['stringOff'] = 50 : $this->conf['stringOff'] = $array['stringOff'];
		$array['enclosure'] == '' ? $this->conf['enclosure'] = '@' : $this->conf['enclosure'] = $array['enclosure'];
		$array['escape'] == '' ? $this->conf['escape'] = '\\' : $this->conf['escape'] = $array['escape'];
	}
	
	
	/**
	 * @param string $file
	 * @param numetric $numString
	 * @param char $delimiter
	 * @param numetric $readOff
	 * @return array
	 */
	public function readString($file, $numStrung, $readOff){
		$name = $this->getFileName($file);
		$this->debug($this->conf['lenghtString']);
		if (!$this->files[$name]) 
			$this->openfile($file, $name);
		$handle = $this->files[$name]['handle'];
		$lenght = $this->conf['lenghtString'];
		$delimiter = $this->conf['delimiter'];
		$enclosure = $this->conf['enclosure'];
		$escape = $this->conf['escape'];
		$csvstring = fgetcsv($handle,$lenght,$delimiter,$enclosure,$escape);
		return $csvstring;
	}
	
	private function openFile($file, $name){
		//$name = $this->getFileName($file);
		if (!$this->files[$name]['is_open']){
			$this->files[$name]['handle'] = fopen($file,'r');
			$this->files[$name]['is_open'] = true;
			return true;
		}else{
			return false;
		}
	}
	
	public function closeFile($name){
		if ($this->files[$name]['handle']){
			fclose($this->files[$name]['handle']);
		}
	}
	
	public function getFileName($fpath){
		$fext  = array_pop(explode('.', $fpath));
		$fname = basename($fpath, '.'.$fext);
		return $fname;
	}
	
	protected function debug($what){
		echo($what);
	}
	
		/**
	 * @author David Thomas
	 * @param file
	 * @return array
	 */
	public function analyse_file($file, $capture_limit = 10) {
		// capture starting memory usage
		$output['time']['start'] = microtime();
		$output['peak_mem']['start'] = memory_get_peak_usage(true);
		// log the limit how much of the file was sampled (in Kb)
		$output['read_kb'] = $capture_limit;
		// read in file
		$fh = fopen($file, 'r');
		$contents = fread($fh, ($capture_limit * 1024)); // in KB
		fclose($fh);
		// specify allowed field delimiters
		$delimiters = array(
			'comma' => ',',
			'semicolon' => ';',
			'tab' => "\t",
			'pipe' => '|',
			'colon' => ':'
		);
		// specify allowed line endings
		$line_endings = array(
			'rn' => "\r\n",
			'n' => "\n",
			'r' => "\r",
			'nr' => "\n\r"
		);
		// loop and count each line ending instance
		foreach ($line_endings as $key => $value) {
			$line_result[$key] = substr_count($contents, $value);
		}
		// sort by largest array value
		asort($line_result);
		
		// log to output array
		$output['line_ending']['results'] = $line_result;
		$output['line_ending']['count'] = end($line_result);
		$output['line_ending']['key'] = key($line_result);
		$output['line_ending']['value'] = $line_endings[$output['line_ending']['key']];
		$lines = explode($output['line_ending']['value'], $contents);
		
		// remove last line of array, as this maybe incomplete?
		array_pop($lines);
		
		// create a string from the legal lines
		$complete_lines = implode(' ', $lines);
		
		// log statistics to output array
		$output['lines']['count'] = count($lines);
		$output['lines']['length'] = strlen($complete_lines);
		
		// loop and count each delimiter instance
		foreach ($delimiters as $delimiter_key => $delimiter) {
			$delimiter_result[$delimiter_key] = substr_count($complete_lines, $delimiter);
		}
		// sort by largest array value
		asort($delimiter_result);
		
		// log statistics to output array with largest counts as the value
		$output['delimiter']['results'] = $delimiter_result;
		$output['delimiter']['count'] = end($delimiter_result);
		$output['delimiter']['key'] = key($delimiter_result);
		$output['delimiter']['value'] = $delimiters[$output['delimiter']['key']];
		// capture ending memory usage
		$output['peak_mem']['end'] = memory_get_peak_usage(true);
		$output['time']['end'] = microtime();
		$output['time']['total'] = $output['time']['end'] - $output['time']['start'];
		return $output;
	}
	
}
?>