<?php
class Database{
	public $link;
	
	public function connect($host, $login, $password, $db_name){
		$link = mysqli_connect($host, $login, $password, $db_name);
		if ($link===false) $this->error_msg();
		$this->link = $link;
		return $link;
	}	
	public function query($query){
		$mysqli_result = mysqli_query($this->link, $query);
		if ($mysqli_result===false) $this->error_msg();
		return $mysqli_result;
	}
	public function get_rows($query){
		$mysqli_result = mysqli_query($this->link, $query);
		if ($mysqli_result===false) $this->error_msg();
		$data = array();
		while ($data[] = mysqli_fetch_assoc($mysqli_result)){}
		array_pop($data);
		return $data;
	}
	public function error_msg(){
		$trace = debug_backtrace();
		exit("Could not execute DB query: <br/>\n\r" .
		$this->link->error . "<br/>\n\r" .
		"in " . $trace[1]['file'] . " on line " . $trace[1]['line']);
	}
	public function esc($value)
	{
		return mysqli_real_escape_string($this->link, $value);
	}
}
