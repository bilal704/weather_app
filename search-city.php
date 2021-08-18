<?php

    if(isset($_GET['term'])){

        $search_term = $_GET['term'];

        $city_data_arr = file_get_contents('cities/city.list.json');

        $city_data_arr = json_decode($city_data_arr, true);

        $response = [];
        
        foreach($city_data_arr as $key => $city_data){

            //find all the matching terms in the city list json and send it in the response
            if(stripos($city_data['name'], $search_term) !== false){

                $response[] = [
                    'city_name' => $city_data['name'],
                    'id' => $city_data['id'],
                    'lat' => $city_data['coord']['lat'],
                    'lon' => $city_data['coord']['lon']
                ];
            }
        }

        if(!empty($response)){

            echo json_encode(['success' => true, 'data' => $response]);
        }
        else{

            echo json_encode(['success' => false, 'data' => '']);            
        }
    }
?>