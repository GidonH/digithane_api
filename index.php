<?php


require('config.php');

$json_arr = [ 'FeaturedItems' => [], 'TopContentItems' => [], 'updates' => [], 'content_items' => [] ];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, "utf8");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// get sp_getFeaturedItems_Serialized

if (!$conn->multi_query("CALL sp_getFeaturedItems_Serialized")) {
    echo "CALL failed: (" . $conn->errno . ") " . $conn->error;
}

do {
    if ($res1 = $conn->store_result()) {

        $temp = $res1->fetch_all(MYSQLI_ASSOC);
        $json_arr['FeaturedItems'] = unserialize($temp[0]['serializedFeaturedItems']);
        $res1->free();

    } else {
        if ($conn->errno) {
            echo "Store failed: (" . $conn->errno . ") " . $conn->error;
        }
    }
} while ($conn->more_results() && $conn->next_result());


// get sp_getTopContentItems

if (!$conn->multi_query("CALL sp_getTopContentItems")) {
    echo "CALL failed: (" . $conn->errno . ") " . $conn->error;
}

do {
    if ($res4 = $conn->store_result()) {

        $rows = $res4->fetch_all();
        $temp = array_column($rows, 0);
        $json_arr['TopContentItems'] = $temp;
        $res4->free();

    } else {
        if ($conn->errno) {
            echo "Store failed: (" . $conn->errno . ") " . $conn->error;
        }
    }
} while ($conn->more_results() && $conn->next_result());


// get updates

if (!$conn->multi_query("CALL sp_getUpdates")) {
    echo "CALL failed: (" . $conn->errno . ") " . $conn->error;
}

do {
    if ($res2 = $conn->store_result()) {
        $temp = $res2->fetch_all(MYSQLI_ASSOC);
        foreach ($temp as $key => $row) {
            $map_unserialized = unserialize($row['MapCoordinates']);
            $temp[$key]['MapCoordinates'] = $map_unserialized ? $map_unserialized : null;
        }
        $json_arr['updates'] = $temp;
        $res2->free();
    } else {
        if ($conn->errno) {
            echo "Store failed: (" . $conn->errno . ") " . $conn->error;
        }
    }
} while ($conn->more_results() && $conn->next_result());


// get content_items
if (!$conn->multi_query("CALL sp_getContentItems")) {
    echo "CALL failed: (" . $conn->errno . ") " . $conn->error;
}

do {
    if ($res3 = $conn->store_result()) {
        $temp = $res3->fetch_all(MYSQLI_ASSOC);
        foreach ($temp as $key => $row) {
            $map_unserialized = unserialize($row['MapCoordinates']);
            $temp[$key]['MapCoordinates'] = $map_unserialized ? $map_unserialized : null;
        }
        $json_arr['content_items'] = $temp;
        $res3->free();
    } else {
        if ($conn->errno) {
            echo "Store failed: (" . $conn->errno . ") " . $conn->error;
        }
    }
} while ($conn->more_results() && $conn->next_result());


$conn->close();

echo json_encode($json_arr);


?>