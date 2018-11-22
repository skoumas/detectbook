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

$router->get('/api/entries/{:id}/', function($id){
	header('Content-Type: application/json');
	$entry = Entry::find($id);
	die(json_encode($entry));
});

//POSTS ^^
$router->post('/api/user/create', function(){

	$post = json_decode(file_get_contents('php://input'));
	$validator = new Validator((array)$post,array(
		"name"=>"required",
		"email"=>"required|email",
		"password"=>"required"
	));

	if (!$validator->isValid()) {
		die(json_encode(["success"=>false,"errors"=>$validator->errors()]));
	}
	$user = new User();
	$user->name = $post->name;
	$user->email = $post->email;
	$user->password = $post->password;
	$user->created_at =  date('Y-m-d H:i:s');
	$user->updated_at = date('Y-m-d H:i:s');
	$user->save();
	die(json_encode(["success"=>true]));
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

	$validator = new Validator((array)$post,array(
		"token"=>"required"
	));

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
	die(json_encode(["success"=>true, "entry"=>$entry]));
});

$router->post('/api/entries/{:id}/delete', function($id){
	$post = json_decode(file_get_contents('php://input'));

	$validator = new Validator((array)$post,array(
		"_token"=>"required"
	));

	if (!$validator->isValid()) {
		die(json_encode(["success"=>false,"errors"=>$validator->errors()]));
	}

	$user = new User();
	$userExists = $user->getByToken($post->_token);

	if (!$userExists) {
		die(json_encode(["success"=>false,"errors"=>"Token is invalid"]));
	}
	$post = json_decode(file_get_contents('php://input'));
	$success = Entry::delete($id);

	die(json_encode(["success"=>$success]));
});

$router->post('/api/entries/{:id}/update', function($id){
	$post = json_decode(file_get_contents('php://input'));

	$validator = new Validator((array)$post,array(
		"_token"=>"required"
	));

	if (!$validator->isValid()) {
		die(json_encode(["success"=>false,"errors"=>$validator->errors()]));
	}

	$user = new User();
	$userExists = $user->getByToken($post->_token);

	if (!$userExists) {
		die(json_encode(["success"=>false,"errors"=>"Token is invalid"]));
	}

	$entry = new Entry();
	$entry->find($id);
	$entry->title = $post->title;
	$entry->content = $post->content;
	$entry->updated_at = date('Y-m-d H:i:s');
	$entry->save();

	die(json_encode(["success"=>true]));
});

$router->post('/api/comment/create', function(){

	$post = json_decode(file_get_contents('php://input'));

	$validator = new Validator((array)$post,array(
		"_token"=>"required"
	));

	if (!$validator->isValid()) {
		die(json_encode(["success"=>false,"errors"=>$validator->errors()]));
	}

	$user = new User();
	$userExists = $user->getByToken($post->_token);

	if (!$userExists) {
		die(json_encode(["success"=>false,"errors"=>"Token is invalid"]));
	}

	$comment = new Comment();
	$comment->user_id = $user->id;
	$comment->content = $post->comment;
	$comment->entry_id = $post->entry_id;
	$comment->created_at =  date('Y-m-d H:i:s');
	$comment->updated_at = date('Y-m-d H:i:s');
	$comment->save();
	die(json_encode(["success"=>true]));
});

$router->match($_SERVER,$_POST);
?>
