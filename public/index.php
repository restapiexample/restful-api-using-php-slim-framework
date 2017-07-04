<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
$corsOptions = array(
    "origin" => "*",
    "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
    "allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
);
$cors = new \CorsSlim\CorsSlim($corsOptions);
 
$app->add($cors);
// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();

function getCustomers() {
    $sql = "select * FROM customer";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $emp = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
       return json_encode($emp);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getCustomer($request) {
    $id = 0;;
    $id =  $request->getAttribute('id');
    if(empty($id)) {
                echo '{"error":{"text":"Id is empty"}}';
    }
    try {
        $db = getConnection();
        $sth = $db->prepare("SELECT * FROM customer WHERE id=$id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $todos = $sth->fetchObject();
                        return json_encode($todos);
    } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
function addCustomer($request) {
    $cust = json_decode($request->getBody());
           
    $sql = "INSERT INTO customer (name, address, country, phone) VALUES (:name, :address, :country, :phone)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $cust->name);
        $stmt->bindParam("address", $cust->address);
        $stmt->bindParam("country", $cust->country);
        $stmt->bindParam("phone", $cust->phone);
        $stmt->execute();
        $cust->id = $db->lastInsertId();
        $db = null;
        echo json_encode($cust);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function updateCustomer($request) {
    $cust = json_decode($request->getBody());
    $id = $request->getAttribute('id');
    $sql = "UPDATE customer SET name=:name, address=:address, country=:country, phone=:phone WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $cust->name);
        $stmt->bindParam("address", $cust->address);
        $stmt->bindParam("country", $cust->country);
        $stmt->bindParam("phone", $cust->phone);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($cust);
    } catch(PDOException $e) {
       echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function deleteCustomer($request) {
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM customer WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        //$stmt->execute();
        $db = null;
        echo '{"error":{"text":"successfully! deleted Records"}}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function getConnection() {
    $dbhost="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="customer_db";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}