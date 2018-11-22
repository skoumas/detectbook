<?
class Validator {

	private $errors;
	private $valid;

	function __construct($data,$rules) {
		$this->errors = [];
		$this->valid = true;
		$this->valid = $this->validate($data,$rules);
	}

	public function isValid() {
		return $this->valid;
	}

	public function errors() {
		return $this->errors;
	}

	private function email($key, $value) {
		$email = filter_var($value, FILTER_SANITIZE_EMAIL);
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			$this->addError($key, " must be an email.");
			return false;
		}
	}

	private function required($key, $value) {
		if($value == '') {
			$this->addError($key, " is required.");
			return false;
		}
		return true;
	}

	private function addError($key,$message) {
		$this->valid = false;
		$this->errors[$key] = $key . " " . $message;
	}

	public function validate($data,$rules) {

		forEach($rules as $rkey=>$rvalue) {
			forEach($data as $dkey=>$dvalue) {
				if ($dkey==$rkey) {
					$validators = explode("|",$rvalue);
					forEach($validators as $validator) {
						self::$validator($dkey, $dvalue);
					}
				}
			}
		}
		return $this->valid;
	}
}
?>
