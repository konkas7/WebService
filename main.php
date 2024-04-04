<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "programma";
$password = "12345";
$dbname = "formula_one_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$array = explode('/', $_SERVER['REQUEST_URI']);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        handlePostRequest($conn);
        break;
    case 'PATCH':
        handlePatchRequest($conn);
        break;
    case 'PUT':
        handlePutRequest($conn);
        break;
    case 'DELETE':
        handleDeleteRequest($conn, $array);
        break;
    case 'GET':
        handleGetRequest($conn, $array);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method Not Allowed"]);
}

$conn->close();

function handlePostRequest($conn) {
    header("Content-Type: application/json");
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $circuito_id = $data["circuito_id"];
    $data_gara = $data["data_gara"];
    $sql = "INSERT INTO gare (circuito_id, data_gara) VALUES ('$circuito_id', '$data_gara')";
    $result = $conn->query($sql);
    http_response_code(200);
}

function handlePatchRequest($conn) {
    header("Content-Type: application/json");
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $gara_id = $data["gara_id"];
    $circuito_id = $data["circuito_id"];
    $data_gara = $data["data_gara"];
    $sql = "UPDATE gare SET circuito_id = '$circuito_id', data_gara = '$data_gara' WHERE gara_id = '$gara_id'";
    $result = $conn->query($sql);
    http_response_code(200);
}

function handlePutRequest($conn) {
    header("Content-Type: application/json");
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $gara_id = $data["gara_id"];
    $circuito_id = $data["circuito_id"];
    $data_gara = $data["data_gara"];
    $check_sql = "SELECT * FROM gare WHERE gara_id = '$gara_id'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        $sql = "UPDATE gare SET circuito_id = '$circuito_id', data_gara = '$data_gara' WHERE gara_id = '$gara_id'";
    } else {
        $sql = "INSERT INTO gare (gara_id, circuito_id, data_gara) VALUES ('$gara_id', '$circuito_id', '$data_gara')";
    }
    $result = $conn->query($sql);
    http_response_code(200);
}

function handleDeleteRequest($conn, $array) {
    header("Content-Type: application/json");
    $gara_id = $array[2];
    $sql = "DELETE FROM gare WHERE gara_id = '$gara_id'";
    $result = $conn->query($sql);
    http_response_code(200);
}

function handleGetRequest($conn, $array) {
    header("Content-Type: application/json");
    if (count($array) == 3 && $array[2] != '') {
        $gara_id = $array[2];
        $sql = "SELECT * FROM gare WHERE gara_id = '$gara_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode($row);
        } else {
            echo json_encode(["message" => "Nessun risultato trovato con ID $gara_id"]);
        }
    } else if (count($array) == 3 && $array[2] == '') {
        $sql = "SELECT * FROM gare";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            echo json_encode(["message" => "Nessun risultato trovato nella tabella gare."]);
        }
    }
}
?>
