<?php
class Model
{
	function __construct() {

	}

	private static $order="order by id DESC";
	private static $hasMany = ["comment"];


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
	private static function tableNameS() {
		return (strtolower(get_called_class()));
	}

	public static function all() {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName() . " order by id DESC" );
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

	public static function getRelationship($key,$id,$db)
	{

		$table = self::pluralize(strtolower(explode("_",$key)[0]));
		$data = $db->get("select * from " . $table  . " where id='$id';" );
		$data = self::returnRelationships($data);
		return $data;
	}

	public static function returnRelationships($data) {

		$db = new Db();
		forEach($data as $row) {
			forEach(self::$hasMany as $hasManyModel) {
			   $returnedData = $db->get("select * from " . self::pluralize(strtolower($hasManyModel))  . " where " .  (self::tableNameS()) . "_id=" . $row->id . ";" );
			    // Need to find better solution for this one.
				//   forEach($returnedData as $row) {
	   			//  forEach($row as $key=>$value) {
				// 	 if (self::keyIsId($key)) {
				// 		 $cleanKey = explode("_",$key)[0];
				// 		 $row->{$cleanKey} = self::getRelationship($key,$value,$db)[0];
				// 		 //$row->{$cleanKey} = self::getRelationship($key);
				// 	 }
				//  }
			 // }
			   $row->{self::pluralize(strtolower($hasManyModel))}  = $returnedData;
		   }
   		}

		forEach($data as $row) {
			 forEach($row as $key=>$value) {
				 if (self::keyIsId($key)) {
					 $cleanKey = explode("_",$key)[0];
					 $row->{$cleanKey} = self::getRelationship($key,$value,$db)[0];
					 //$row->{$cleanKey} = self::getRelationship($key);
				 }
			 }
		}

		return $data;
	}

	public static function delete($id) {
		$db = new Db();
		$exists = $db->query("SELECT * FROM " . self::tableName() .  " where id='$id';");

		if (!empty($exists)) {
			$db->query("DELETE FROM " . self::tableName() .  " where id='$id';");
			return true;
		}
		return false;

	}

	// TODO
	public function find($id) {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName()  . " where id='$id'; " );
		forEach($data[0] as $key=>$value) {
			$this->{$key} = $value;
		}
		$data = self::returnRelationships($data);
		return (object)$this;
	}

	public static function where($param,$value) {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName()  . " where $param='$value' " . " order by id DESC" . "; " );
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
