<?php
class Model
{
	function __construct() {

	}

	//private static $order = "order by id DESC";
	private static $hasMany = [];

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

	/**
	 * Converts the model name convention into the mysql table convention name
	 *
	 * @return String
	*/
	private static function tableName() {
		return self::pluralize(strtolower(get_called_class()));
	}

	/**
	 * Gets the table name in singular
	 *
	 * @return String
	*/
	private static function tableNameS() {
		return (strtolower(get_called_class()));
	}

	/**
	 * Gets all entries of one table/model
	 *
	 * @return data
	*/
	public static function all() {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName() . " order by id DESC" );
		$data = self::returnRelationships($data);
		return $data;
	}

	/**
	 * Checks if a string is like *_id
	 *
	 * @param key   $id The string to be checked
	 * @return Boolean
	*/
	public static function keyIsId($key) {
	    $length = strlen("_id");
	    if ($length == 0) {
	        return true;
	    }
	    return (substr($key, -$length) === "_id");
	}

	/**
	 * Return one to one data
	 *
	 * @param key   $id The key of the entry
	 * @param id   $id The id of the entry
	 * @param db   $id The db object that we use to avoid
	 * @return Object
	*/
	public static function getRelationship($key,$id,$db) {
		$table = self::pluralize(strtolower(explode("_",$key)[0]));
		$data = $db->get("select * from " . $table  . " where id='$id';" );
		return $data;
	}

	/**
	 * Deletes the hasMany and the one to one relationships
	 *
	 * @param data   $id Data being passes usually recursively
	 * @return data
	*/
	public static function returnRelationships($data) {
		$db = new Db();
		// If the field is _id then find the table and that entry and return it to the object.
		forEach($data as $row) {
			 forEach($row as $key=>$value) {
				 if (self::keyIsId($key)) {
					 $cleanKey = explode("_",$key)[0];
					 $row->{$cleanKey} = self::getRelationship($key,$value,$db)[0];
					 //$row->{$cleanKey} = self::getRelationship($key);
				 }
			 }
		}
		// Get the has many getRelationship
		if(property_exists(get_called_class(), 'hasMany')) {
			forEach($data as $row) {
				forEach(static::$hasMany as $hasManyModel) {
					if (!array_key_exists(self::pluralize(strtolower($hasManyModel)),$row)) {
						if (!array_key_exists(self::tableNameS(),$row)) {
							$returnedData = $db->get("SELECT * FROM " . self::pluralize(strtolower($hasManyModel))  . " WHERE " .  (self::tableNameS()) . "_id=" . $row->id . ";");
							$returnedData = self::returnRelationships($returnedData);
							$row->{self::pluralize(strtolower($hasManyModel))}  = $returnedData;
						}
					}
			   }
	   		}
		}
		return $data;
	}

	/**
	 * Deletes one entry
	 *
	 * @param id   $id The id of the entry
	 * @return Boolean
	*/
	public static function delete($id) {
		$db = new Db();
		$exists = $db->query("SELECT * FROM " . self::tableName() .  " where id='$id';");
		if (!empty($exists)) {
			$db->query("DELETE FROM " . self::tableName() .  " where id='$id';");
		}
		//Delete all related data based on the relationship described here
		if(property_exists(get_called_class(), 'hasMany')) {
			forEach(static::$hasMany as $hasManyModel) {
				 $returnedData = $db->query("DELETE FROM " . self::pluralize(strtolower($hasManyModel)) .  " WHERE " . (self::tableNameS()) . "_id=" . $id . ";");
			}
		}
		return true;
	}

	/**
	 * Find one entry based on ID and returns it
	 *
	 * @param id   $id The id of the entry
	 * @return Object
	*/
	public function find($id) {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName()  . " where id='$id'; " );
		forEach($data[0] as $key=>$value) {
			$this->{$key} = $value;
		}
		$data = self::returnRelationships($data);
		return (object)$this;
	}

	/**
	 * Find one entry based on a specific parameter
	 *
	 * @param id   $id The id of the entry
	 * @return Object
	*/
	public static function where($param,$value) {
		$db = new Db();
		$data = $db->get("select * from " . self::tableName()  . " where $param='$value' " . " order by id DESC" . "; " );
		$data = self::returnRelationships($data);
		return $data;
	}

	/**
	 * Saves on entry in the database or updates it if exists aleady
	 *
	 * @param id   $id The id of the entry
	 * @return Object
	*/
	public function save() {
		// TODO fix the exists[0] to something more proper
		$db = new Db();
		$exists = $db->query("Select * from " . $this->tableName() . " where id='" . $this->id . "';");
		if ($exists[0]!=[]) {
			$db->update((array)$this,$this->tableName());
		} else {
			$db->insert((array)$this,$this->tableName());
		}
	}
}
?>
