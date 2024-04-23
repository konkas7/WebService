<?php
//connessione database
$servername = "localhost";
$username = "programma";
$password = "12345";
$dbname = "formula_one_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// controllo connessione 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// processo richiesta
$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($method == "GET") {
    if ($uri[3] == "circuiti") {
        if (count($uri) == 4 || (count($uri) == 5 && $uri[4] == "")) {
            // prendi tutti i circuiti
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
        } else if (count($uri) == 5) {
            // circuito specifico da id
            $circuit_id = $uri[4];
            $sql = "SELECT * FROM circuiti WHERE circuito_id = '$circuit_id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $circuit = $result->fetch_assoc();
                header("Content-Type: application/json");
                echo json_encode($circuit);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Circuit not found."));
            }
        }
    } else {
        // path non valido
        http_response_code(404);
        echo json_encode(array("message" => "Invalid path."));
    }
} elseif ($method == "POST") {
    // aggiungi un nuovo circuito
    // formato json base {"nome_circuito": "Monaco Grand Prix"}
    $data = json_decode(file_get_contents("php://input"), true);
    $nome_circuito = $data['nome_circuito'];

    $sql = "INSERT INTO circuiti (nome_circuito) VALUES ('$nome_circuito')";

    if ($conn->query($sql) === TRUE) {
        http_response_code(201); // creato
        echo json_encode(array("message" => "Circuit added successfully."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error adding circuit: " . $conn->error));
    }
} elseif ($method == "PUT") {
    // aggiorna circuito
    // formato json base: {"nome_circuito": "New Circuit Name"}
    $data = json_decode(file_get_contents("php://input"), true);
    $circuit_id = $uri[4];
    $nome_circuito = $data['nome_circuito'];

    $sql = "UPDATE circuiti SET nome_circuito = '$nome_circuito' WHERE circuito_id = '$circuit_id'";

    if ($conn->query($sql) === TRUE) {
        http_response_code(200); // OK
        echo json_encode(array("message" => "Circuit updated successfully."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error updating circuit: " . $conn->error));
    }
} elseif ($method == "DELETE") {
    // elimina circuito
    $circuit_id = $uri[4];

    $sql = "DELETE FROM circuiti WHERE circuito_id = '$circuit_id'";

    if ($conn->query($sql) === TRUE) {
        http_response_code(200); // OK
        echo json_encode(array("message" => "Circuit deleted successfully."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error deleting circuit: " . $conn->error));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}

$conn->close();
?>
