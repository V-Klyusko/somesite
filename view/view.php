<?php
class View{
	public $data = [];

	public function render ($tpl_name, $data = []) {
		foreach ($data as $key => $value) {
			$this->data[$key] = $this->escape($value);
		}
		extract($this->data, EXTR_PREFIX_SAME, '_');
		ob_start();
		require 'view/template/'.$tpl_name.'.phtml';
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Escapes data for using it in templates.
	 * Data can be scalar or an array.
	 * @param mixed $data Data to escape.
	 * @return mixed Escaped data.
	 */ 
	public function escape ($data) {
		if (gettype($data)==='array') foreach ($data as $key => $value) {
			if (isset($value['escape']) && $value['escape']===false) {
				$data[$key] = $value['value'];
			}
			else $data[$key] = $this->escape($value);
		}
		if (gettype($data)==='string') {
			$data = htmlspecialchars($data, ENT_QUOTES);
		}
		return $data;
	}

	public function setData ($key, $value, $escape = true) {
		if ($escape) escape($value);
		return $this->data[$key] = $value;
	}
}