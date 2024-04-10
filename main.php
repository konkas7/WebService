<?php
// Database connection parameters
$servername = "localhost";
$username = "programma";
$password = "12345";
$dbname = "formula_one_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);





if ($method === "GET") {
    if ($uri[3] === "circuits") {

        $sql = "SELECT * FROM circuiti";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $circuits = array();
            while ($row = $result->fetch_assoc()) {
                $circuits[] = $row;
            }
            header("Content-Type: application/json");
            echo json_encode($circuits);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No circuits found."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Invalid path."));
    }
} elseif ($method === "POST") {

    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    if ($uri[3] === "circuits" && $uri[4] === "add") {
        $name = $data['name'];
        
        $sql = "INSERT INTO circuiti (nome_circuito) VALUES ('$name')";
        
        if ($conn->query($sql) === TRUE) {
            http_response_code(201); 
            echo json_encode(array("message" => "Circuit added successfully."));
        } else {
            http_response_code(500); 
            echo json_encode(array("message" => "Error adding circuit: " . $conn->error));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid path for POST method."));
    }
} elseif ($method === "PUT") {

    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    if ($uri[3] === "circuits" && $uri[4] === "update") {
        $circuit_id = $uri[5];
        $name = $data['name'];
        
        $sql = "UPDATE circuiti SET nome_circuito='$name' WHERE circuito_id='$circuit_id'";
        
        if ($conn->query($sql) === TRUE) {
            http_response_code(200); 
            echo json_encode(array("message" => "Circuit updated successfully."));
        } else {
            http_response_code(500); 
            echo json_encode(array("message" => "Error updating circuit: " . $conn->error));
        }
    } else {
        http_response_code(400); 
        echo json_encode(array("message" => "Invalid path for PUT method."));
    }
} elseif ($method === "DELETE") {

    if ($uri[3] === "circuits" && $uri[4] === "delete") {
        $circuit_id = $uri[5];
        
        $sql = "DELETE FROM circuiti WHERE circuito_id='$circuit_id'";
        
        if ($conn->query($sql) === TRUE) {
            http_response_code(200); 
            echo json_encode(array("message" => "Circuit deleted successfully."));
        } else {
            http_response_code(500); 
            echo json_encode(array("message" => "Error deleting circuit: " . $conn->error));
        }
    } else {
        http_response_code(400); 
        echo json_encode(array("message" => "Invalid path for DELETE method."));
    }
} else {
    http_response_code(405); 
    echo json_encode(array("message" => "Method not supported."));
}

// Closing the connection
$conn->close();
?>
