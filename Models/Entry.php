<?
class Entry extends Model {
	// Our entry has many comments.
	public static $hasMany = ["comment"];
}
?>
