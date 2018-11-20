<?php
class Model
{
	function __construct() {

	}

	//For making life better!
	private static function pluralize($string) {
	    $last_letter = strtolower($string[strlen($string)-1]);
	    switch($last_letter) {
	        case 'y':
	            return substr($string,0,-1).'ies';
	        case 's':
	            return $string.'es';
	        default:
	            return $string.'s';
	    }
	}

	private static function tableName() {
		return self::pluralize(strtolower(get_called_class()));
	}

	public static function all() {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName() );
		$data = self::returnRelationships($data);
		return $data;
	}

	public static function keyIsId($key)
	{
	    $length = strlen("_id");
	    if ($length == 0) {
	        return true;
	    }
	    return (substr($key, -$length) === "_id");
	}

	public static function getRelationship($key)
	{

	}

	public static function returnRelationships($data) {
		forEach($data as $row) {
			 forEach($row as $key=>$value) {

				 if (self::keyIsId($key)) {
					 $row->{$key} = self::getRelationship($key);
				 }
			 }
		}
		return $data;
	}

	public static function where($param,$value) {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName()  . " where $param='$value';" );
		$data = self::returnRelationships($data);
		return $data;
	}

	public function save() {
		$db = new Db();
		$exists = $db->query("Select * from " . $this->tableName() . " where id='" . $this->id . "';");

		if ($exists[0]!=[]) {
 
			// update
			$db->update((array)$this,$this->tableName());
		} else {
			$db->insert((array)$this,$this->tableName());
		}
	}

}
?>
