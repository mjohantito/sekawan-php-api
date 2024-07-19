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

function getVehicleList(){
    global $conn;

    $query = "SELECT * FROM vehicle";
    $query_run = mysqli_query($conn, $query);

    if($query_run){

        if(mysqli_num_rows($query_run) > 0){

            // $res = mysqli_fetch_all($query_run,MYSQLI_ASSOC);
            // $data = [
            //     // 'status' => 200,
            //     // 'message' => 'Vehicle Fetched Successfullly',
            //     // 'data' => $res
            // ];
            // header("HTTP/1.0 200 Vehicle Fetched Successfully");
            // header('Content-Type: application/json');

            // //ignore bracketsquare
            // $json_res = json_encode($res);
            // // $json_res = trim($json_res,'[]');
            // // $json_res = str_replace(['[',']','{','}'], '', $json_res);
            
            // echo $json_res;
            // // echo json_encode($res);

            $res = [];
            while($row = mysqli_fetch_assoc($query_run)){
                $row['vehicletype'] = (int)$row['vehicletype'];
                $row['vehicleownership'] = (int)$row['vehicleownership'];
                $row['vehiclestatus'] = (int)$row['vehiclestatus'];

                $date = new DateTime($row['vehiclelastservice']);
                $row['vehiclelastservice'] = $date->format('Y-m-d');
                
                $res[] = $row;
            }

            header("HTTP/1.0 200 Vehicle Fetched Successfully");
            header('Content-Type: application/json');

            echo json_encode($res);
            
        }else{
            $data = [
                'status' => 404,
                'message' => 'No Vehicle Found',
            ];
            header("HTTP/1.0 404 Not Vehicle Found");
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

function storeVehicle($vehicleInput){
    global $conn;

    $vehicleid = mysqli_real_escape_string($conn, $vehicleInput['vehicleid']);
    $vehicleinfo = mysqli_real_escape_string($conn, $vehicleInput['vehicleinfo']);
    $vehicletype = mysqli_real_escape_string($conn, $vehicleInput['vehicletype']);
    $vehicleownership = mysqli_real_escape_string($conn, $vehicleInput['vehicleownership']);
    $vehiclelastservice = mysqli_real_escape_string($conn, $vehicleInput['vehiclelastservice']);
    $vehiclestatus = mysqli_real_escape_string($conn, $vehicleInput['vehiclestatus']);

    if(empty(trim($vehicleid))){
        return error422('Enter Vehicle ID');
    }elseif(empty(trim($vehicleinfo))){
        return error422('Enter Vehicle Info');
    }elseif(empty(trim($vehicletype))){
        return error422('Enter Vehicle Type');
    }elseif(empty(trim($vehicleownership))){
        return error422('Enter Vehicle Ownership');
    }elseif(empty(trim($vehiclelastservice))){
        return error422('Enter Vehicle Last Service');
    }elseif(empty(trim($vehiclestatus))){
        return error422('Enter Vehicle Status');
    }else{
        $query = "INSERT INTO vehicle(vehicleid, vehicleinfo, vehicletype, vehicleownership, vehiclelastservice, vehiclestatus) VALUES (
            UPPER('$vehicleid'),
            UPPER('$vehicleinfo'),
            CAST('$vehicletype' AS UNSIGNED),
            CAST('$vehicleownership' AS UNSIGNED),
            STR_TO_DATE('$vehiclelastservice', '%Y-%m-%d'),
            CAST('$vehiclestatus' AS UNSIGNED))";
    
        $result = mysqli_query($conn, $query);
        if($result){

            $data = [
                'status' => 201,
                'message' => 'Vehicle Stored Successfully',
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