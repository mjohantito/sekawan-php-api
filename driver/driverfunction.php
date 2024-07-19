<?php

require '../inc/dbcon.php';

function error422($message){

    $data = [
        'status' => 422,
        'message' => $message,
    ];

    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

function getDriverList(){
    global $conn;

    $query = "SELECT * FROM driver";
    $query_run = mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            $res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);
            $data = [
                // 'status' => 200,
                // 'message' => 'Driver Fetched Successfullly',
                // 'data' => $res
            ];
            header("HTTP/1.0 200 Driver Fetched Successfully");
            header('Content-Type: application/json');

            //ignore bracketsquare
            $json_res = json_encode($res);
            // $json_res = trim($json_res,'[]');
            // $json_res = str_replace(['[',']','{','}'], '', $json_res);
            
            echo $json_res;
            // echo json_encode($res);

            header("HTTP/1.0 200 Driver Fetched Successfully");
            header('Content-Type: application/json');

            echo json_encode($res);
            
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Driver Found',
            ];
            header("HTTP/1.0 404 Not Driver Found");
            return json_encode($data);
        }

    }else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

function storeDriver($driverInput){
    global $conn;

    $driverid = mysqli_real_escape_string($conn, $driverInput['driverid']);
    $drivername = mysqli_real_escape_string($conn, $driverInput['drivername']);
    $driverage = mysqli_real_escape_string($conn, $driverInput['driverage']);
    $driverstatus = mysqli_real_escape_string($conn, $driverInput['driverstatus']);
    

    if(empty(trim($driverid))){
        return error422('Enter Driver ID');
    }elseif(empty(trim($drivername))){
        return error422('Enter Driver Name');
    }elseif(empty(trim($driverage))){
        return error422('Enter Driver Age');
    }elseif(empty(trim($driverstatus))){
        return error422('Enter Driver Status');
    }else{
        $query = "INSERT INTO driver(driverid, drivername, driverage, driverstatus) VALUES (
            UPPER('$driverid'),
            UPPER('$drivername'),
            CAST('$driverage' AS UNSIGNED),
            CAST('$driverstatus' AS UNSIGNED))";
    
        $result = mysqli_query($conn, $query);
        if($result){

            $data = [
                'status' => 201,
                'message' => 'Driver Stored Successfully',
            ];

            header("HTTP/1.0 201 Stored");
            return json_encode($data);
        }else{

            error_log("SQL Error: " . mysqli_error($conn));

            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];

            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }
}

?>