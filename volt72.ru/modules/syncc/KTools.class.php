<?
class KTools {
	/**
	 * @author Krox
	 * Tools for parse very long csvfiles
	 * 20110812
	 */
	var $fileHandle = false;
	var $fileOffsetHandle = false;
	var $fileSize = 0;
	var $percentExec = 0;
	var $firstLine = true;	//trig
	var $countString = 0;
	var $config;
	var $offset = 0;
	var $loffset = 0;
	var $globarr = array();
	var $arrayReplace = array(',','"','*','\'');
	var $err = false;
	
	function KTools(){
		$this->debug('inst');
		$this->configure();
	}
	
	/**
	 * @param array $array
	 * lineDelilm - def \r\n 
	 * delimiter - def |
	 * escape - def @
	 * length - def 1024 
	 * writeOffset - def false, write offset to file
	 */
	function configure($array=''){
		$this->config['lineDelim'] = $array['lineDelim'] ? $array['lineDelim'] : "\r\n";
		$this->config['delimiter'] = $array['delimiter'] ? $array['delimiter'] : "|";
		$this->config['escape'] = $array['escape'] ? $array['escape'] : '@';
		$this->config['length'] = $array['length'] ? $array['length'] : 1024;
		$this->config['writeOffset'] = $array['writeOffset'] ? $array['writeOffset'] : true;
		$this->config['getPercentEx'] = $array['getPercentEx'] ? $array['getPercentEx'] : false;
	}
	
	function getFileSize($file){
		$this->fileSize = filesize($file);
	}
	
	function getPercentExecute(){
		$percentEx = $this->loffset / $this->fileSize;
		return $this->percentExec = round($percentEx,2)*100;
		//die(print("this is opa".$this->percentExec));
	} 
	protected function openFile($file,$par){
		//$this->debug('open '.$file);
		if (!$file) return false;
		if (!file_exists($file)) 
			return $this->errors(1,$file);
		$this->fileHandle = fopen($file,$par);
		$this->getFileSize($file);
		return true;
	}
	
	public function readString($file,$offset=0,$lenstr=0){
		$offset = $offset ? $offset : $this->offset;
		$lenstr = $lenstr ? $lenstr : $this->config['length'];
		
		//if($this->config['writeOffset']==true){
			if (!is_resource($this->fileHandle)){
				if (!$this->openFile($file,'r')) return $this->errors(1);
			}
		//}
		
		if($this->firstLine == true ){
			$this->firstLine = false;
			$offset = $this->getOffset($file);
			if($offset==0){
				fgets($this->fileHandle,$lenstr); //skip headers
			}else{
				fseek($this->fileHandle, $offset);
			}
		}
		$data = fgets($this->fileHandle,$lenstr);
		//if($data) return false;
		return $data;
	}
	
	function openOffsetFile($file,$par){
		if(!is_resource($this->fileOffsetHandle)){
			if(file_exists($file)){
				$this->fileOffsetHandle = fopen($file,$par);
			}else{
				$this->fileOffsetHandle = fopen($file,'w+');
			}
		}
	}
		
	function closeOffsetFile(){
		if(!is_resource($this->fileOffsetHandle)){
			fclose($this->fileOffsetHandle);
		}
	}
	
	function getOffset($file){
		$this->openOffsetFile($file.'.offset', 'r+');
		$this->offset = $offset = fgets($this->fileOffsetHandle,12); //with reserve
		$this->closeOffsetFile();
		//$this->debug($offset);
		return $offset;
	}
	
	/**
	 * @param string $file
	 * @param string $nic handle of file
	 */
	public function putOffset($file){
		if(!is_resource($this->fileOffsetHandle))
			$this->openFile($file.'.offset', 'offset','w+');
		$this->loffset = $offset = ftell($this->fileHandle);
		$this->percentExec = $this->getPercentExecute();
		fseek($this->fileOffsetHandle,0);
		if (!fputs($this->fileOffsetHandle, $offset,strlen($offset)))return $this->errors(3);
		//fclose($this->fileOffsetHandle);
	}
	
	public function stringToAssoc($str,$headers=''){
		$str = str_replace($this->arrayReplace, '.', $str);
		$str = explode($this->config['delimiter'],$str);
		if ($headers!=''){
			foreach ($str as $key => $value){
				$assoc[$headers[$key]] = $value;
			}
		}else{
			return $str;
		}
		return $assoc;
	}
	
	//old
	public function closeFile($nic){
		if (!is_resource($this->fileHandle[$nic])) return $this->errors(4,$nic);
		fclose($this->fileHandle[$nic]);
		$this->fileHandle = false;
	}
	
	protected function errors($n,$str=''){
		$desc = array(
			1=>'File not found',
			2=>'Can\'t open file',
			3=>'Can\'t write to file',
			4=>'This not hadle of file'
		);
		//$this->debug($desc[$n].': '.$str);
		$this->err = $desc[$n].': '.$str;
		return false;
	}
	
	public function debug($var){
		switch ($var) {
			case 'memory_peak':
				$debug['mempeak'] = memory_get_usage();
				$out = 'memory peak: ';
				$out .= $debug['mempeak'];
			break;
			case 'memory':
				$debug['mem'] = memory_get_usage();
				$out = 'memory: ';
				$out .= $debug['mem'];
			break;
			case 'time':
				$debug['time'] = $this->getMicrotime();
				$out = 'time: ';
				$out .= $debug['time'] - $this->debug['time'];
			break;
			case 'inst':
				$debug['mem'] = memory_get_usage();
				$debug['time'] = $this->getMicrotime();
				$this->debug['time'] = $debug['time'];
				$this->debug['mem'] = $debug['mem'];
			break;
			default:
				echo(''.$var.'<br/>');
			break;
		}
		if ($out){
			echo(''.$out.'<br/>');
		}
	}
	
	function getMicrotime() {
		list ($usec, $sec)= explode(' ', microtime());
		return ((float) $usec + (float) $sec);
	}
};
?>