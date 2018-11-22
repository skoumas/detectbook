<?
class Db {

	private $pdo;

	function __construct() {
		$this->connect();
	}

	private function connect() {

		$this->pdo = new PDO("mysql:host=db;dbname=detectbook",$_ENV['USER'],$_ENV['PASSWORD'], $options);
	}

	public function query($query) {
		$stmt = $this->pdo->query($query);
		if (!$stmt) {
			die("Db error");
		}
		$final = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$final[] = (object) $row;
		}

		return $final;
	}

	public function get($query){
		$stmt = $this->pdo->query($query);
		if (!$stmt) {
			die("Db error");
		}
		$final = [];
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$final[] = (object) $row;
		}

		return $final;
	}

	public function insert($data,$table) {
		 
		$statement = $this->pdo->prepare('INSERT INTO '.$table.' (' . implode(", ", array_keys($data)) . ') VALUES (' . implode(", ", array_map(function($x){return "?";},$data)) . ');');
		$result = $statement->execute(array_values($data));
	}

	public function update($data, $table) {

		$statement = $this->pdo->prepare('UPDATE ' . $table .' SET ' . implode(", ", array_map(function($x){return $x."=?";},array_keys($data))) . ' WHERE id=' . $data["id"]);
		$result = $statement->execute(array_values($data));
	}

	public function close() {
		$this->pdo = null;
	}
}
?>
