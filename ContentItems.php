<?php


require('config.php');

$json_arr = [];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, "utf8");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// get updates

//if (!$conn->multi_query("CALL sp_getUpdates")) {
//    echo "CALL failed: (" . $conn->errno . ") " . $conn->error;
//}
//
//do {
//    if ($res2 = $conn->store_result()) {
//        $temp = $res2->fetch_all(MYSQLI_ASSOC);
//        foreach ($temp as $key => $row) {
//            $map_unserialized = unserialize($row['MapCoordinates']);
//            $temp[$key]['MapCoordinates'] = $map_unserialized ? $map_unserialized : null;
//        }
//        $json_arr['updates'] = $temp;
//        $res2->free();
//    } else {
//        if ($conn->errno) {
//            echo "Store failed: (" . $conn->errno . ") " . $conn->error;
//        }
//    }
//} while ($conn->more_results() && $conn->next_result());


// get content_items

if (!$conn->multi_query("CALL sp_getContentItems")) {
    echo "CALL failed: (" . $conn->errno . ") " . $conn->error;
}

do {
    if ($res3 = $conn->store_result()) {
        $json_arr  = $res3->fetch_all(MYSQLI_ASSOC);

        $res3->free();
    } else {
        if ($conn->errno) {
            echo "Store failed: (" . $conn->errno . ") " . $conn->error;
        }
    }
} while ($conn->more_results() && $conn->next_result());


$conn->close();

//$filehandle = fopen('hello.csv', 'w+');
//fputcsv($filehandle, array_keys($json_arr[0]));
$out = fopen('php://output', 'w');
fputcsv($out, array_keys($json_arr[0]));
echo '<br>';
foreach($json_arr as $row){
    fputcsv($out, $row);
    echo '<br>';
}

fclose($out);
//function generateCsv($data, $delimiter = ',', $enclosure = '"') {
//    $handle = fopen('php://temp', 'r+');
//    foreach ($data as $line) {
//        fputcsv($handle, $line, $delimiter, $enclosure);
//    }
//    rewind($handle);
//    $contents="";
//    while (!feof($handle)) {
//        $contents .= fread($handle, 8192);
//    }
//    fclose($handle);
//    return $contents;
//}
//echo generateCsv($json_arr);
//echo json_encode($json_arr);


?>