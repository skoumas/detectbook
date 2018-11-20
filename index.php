<?
include("_variables.php");
include("Helpers/Validator.php");
include("Helpers/Db.php");
include("Helpers/Router.php");

function __autoload($class)
{
    $parts = explode('\\', $class);
    require "./Models/".  end($parts) . '.php';
}

$router = new Router();
header('Access-Control-Allow-Origin: *');
//Gets
$router->get('/', function(){
	echo ("<img src='Images/ohhimark.jpg'/><br>");
});

$router->get('/api/test', function(){
	echo ("Im alive!");
});

$router->get('/api/guestbooks', function(){
	header('Content-Type: application/json');
	$guestbooks = Guestbook::all();
	die(json_encode($guestbooks));
});

$router->get('/api/users', function(){
	header('Content-Type: application/json');
	$user = User::all();
	die(json_encode($user));
});

$router->get('/api/entries', function(){
	header('Content-Type: application/json');
	$entries = Entry::all();
	die(json_encode($entries));
});

$router->get('/api/guestbook/{:id}/entries', function($id){
	header('Content-Type: application/json');
	$entries = Entry::where("guestbook_id",$id);
	die(json_encode($entries));
});

$router->get('/api/entry/{:id}/comments', function($id){
	header('Content-Type: application/json');
	$comments = Comment::where("comment_id",$id);
	die(json_encode($comments));
});

//POSTS ^^
$router->post('/api/user/create', function(){

	$validator = new Validator($_POST,array(
		"name"=>"required",
		"password"=>"required",
		"email"=>"required|email"
	));

	if ($validator->isValid()) {
		die(json_encode($validator->errors()));
	}
	$user = new User();
	$user->name = $_POST["name"];
	$user->email = $_POST["email"];
	$user->password = $_POST["password"];
	$user->save();
});

$router->post('/api/user/login', function(){
	$post = json_decode(file_get_contents('php://input'));

	$validator = new Validator($post,array(
		"email"=>"required|email",
		"password"=>"required"
	));

	if (!$validator->isValid()) {
		die(json_encode($validator->errors()));
	}
	$user = new User();
	if ($user->login($post->email,$post->password)) {
		die(json_encode(["success"=>true,"token"=>$user->token]));
	} else {
		die(json_encode(["success"=>false]));
	}
});

$router->post('/api/checkToken', function(){
	$post = json_decode(file_get_contents('php://input'));
	// $validator = new Validator((array)$post,array(
	// 	"token"=>"required"
	// ));
	//
	// if ($validator->isValid()) {
	// 	die(json_encode($validator->errors()));
	// }
	$user = new User();
	$user->getByToken($post->token);
	if ($user) {
		die(json_encode(["success"=>true, "user"=>$user]));
	} else {
		die(json_encode(["success"=>false]));
	}
});

$router->post('/api/entry/create', function(){
	$post = json_decode(file_get_contents('php://input'));

	$entry = new Entry();
	$entry->guestbook_id = 1;
	$entry->user_id = $post->user_id;
	$entry->content = $post->content;
	$entry->title = $post->title;
	$entry->created_at =  date('Y-m-d H:i:s');
	$entry->updated_at = date('Y-m-d H:i:s');
	$entry->save();
});

$router->post('/api/comment/new', function(){
	$entry = new Entry();
	$entry->guestbook_id = 1;
	$entry->user_id = 1;
	$entry->content = "Test";
	$entry->save();
});

$router->match($_SERVER,$_POST);
?>
