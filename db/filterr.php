<?php 

require_once 'db/conectie.php';

$zoek = $_GET['zoek'] ?? '';
$type = $_GET['type'] ?? '';
$werelddeel = $_GET['werelddeel'] ?? '';
$bestaat = $_GET['bestaat'] ?? '';
$sort = $_GET['sort'] ?? '';

$query = "SELECT * FROM wonders WHERE 1";
$params = [];

if ($zoek) {
    $query .= " AND name LIKE :zoek";
    $params[':zoek'] = "%$zoek%";
}
if ($type) {
    $query .= " AND type = :type";
    $params[':type'] = $type;
}
if ($werelddeel) {
    $query .= " AND continent = :werelddeel";
    $params[':werelddeel'] = $werelddeel;
}
if ($bestaat !== '') {
    $query .= " AND bestaat_nog = :bestaat";
    $params[':bestaat'] = $bestaat;
}
if ($sort === 'jaar') {
    $query .= " ORDER BY bouwjaar ASC";
} elseif ($sort === 'naam') {
    $query .= " ORDER BY name ASC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$wereldwonderen = $stmt->fetchAll();


?>