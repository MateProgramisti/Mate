<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitor_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

function getMonitors($conn) {
    $sql = "SELECT * FROM monitors WHERE stock > 0 ORDER BY name ASC";
    $result = $conn->query($sql);
    
    $monitors = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $monitors[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'brand' => $row['brand'],
                'price' => (float)$row['price'],
                'image' => $row['image'],
                'screenSize' => $row['screen_size'],
                'resolution' => $row['resolution'],
                'refreshRate' => $row['refresh_rate'],
                'panelType' => $row['panel_type'],
                'stock' => (int)$row['stock']
            ];
        }
    }
    
    return $monitors;
}

function getMonitorById($conn, $id) {
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM monitors WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'brand' => $row['brand'],
            'price' => (float)$row['price'],
            'image' => $row['image'],
            'screenSize' => $row['screen_size'],
            'resolution' => $row['resolution'],
            'refreshRate' => $row['refresh_rate'],
            'panelType' => $row['panel_type'],
            'stock' => (int)$row['stock']
        ];
    }
    
    return null;
}

function searchMonitors($conn, $query) {
    $query = $conn->real_escape_string($query);
    $sql = "SELECT * FROM monitors WHERE name LIKE '%$query%' OR brand LIKE '%$query%' ORDER BY name ASC";
    $result = $conn->query($sql);
    
    $monitors = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $monitors[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'brand' => $row['brand'],
                'price' => (float)$row['price'],
                'image' => $row['image'],
                'screenSize' => $row['screen_size'],
                'resolution' => $row['resolution'],
                'refreshRate' => $row['refresh_rate'],
                'panelType' => $row['panel_type'],
                'stock' => (int)$row['stock']
            ];
        }
    }
    
    return $monitors;
}

function filterMonitorsByBrand($conn, $brand) {
    $brand = $conn->real_escape_string($brand);
    $sql = "SELECT * FROM monitors WHERE brand = '$brand' ORDER BY name ASC";
    $result = $conn->query($sql);
    
    $monitors = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $monitors[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'brand' => $row['brand'],
                'price' => (float)$row['price'],
                'image' => $row['image'],
                'screenSize' => $row['screen_size'],
                'resolution' => $row['resolution'],
                'refreshRate' => $row['refresh_rate'],
                'panelType' => $row['panel_type'],
                'stock' => (int)$row['stock']
            ];
        }
    }
    
    return $monitors;
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $monitor = getMonitorById($conn, $_GET['id']);
            if ($monitor) {
                echo json_encode(['success' => true, 'data' => $monitor]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Monitor not found']);
            }
        } elseif (isset($_GET['search'])) {
            $monitors = searchMonitors($conn, $_GET['search']);
            echo json_encode(['success' => true, 'data' => $monitors]);
        } elseif (isset($_GET['brand'])) {
            $monitors = filterMonitorsByBrand($conn, $_GET['brand']);
            echo json_encode(['success' => true, 'data' => $monitors]);
        } else {
            $monitors = getMonitors($conn);
            echo json_encode(['success' => true, 'data' => $monitors]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

$conn->close();
?>
