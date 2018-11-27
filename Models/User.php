<?
class User extends Model {

	private function generateToken() {
		return substr( "abcdefghijklmnopqrstuvwxyz" ,mt_rand( 0 ,25 ) ,1 ) .substr( md5( time( ) ) ,1 );
	}

	public function login($email,$password) {
		$db = new Db;

		$options = [
			'cost' => 12,
		];
 
		$result = $db->query("Select * from users where email='$email' AND password='$password';");
		if ($result!=[]) {
			forEach($result[0] as $key=>$value) {
				$this->{$key} = $value;
			}
			$this->token = $this->generateToken();
			$this->save();
			return true;
		} else {
			return false;
		}
	}

	public function getByToken($token) {
		$db = new Db;
		$result = $db->query("Select * from users where token='" .$token . "';");

		if ($result!=[]) {
			forEach($result[0] as $key=>$value) {
				$this->{$key} = $value;
			}
			return true;
		} else {
			return false;
		}
	}
}
?>
