<?php
    
    if(isset($_POST)){

        define('APP_ID', '15e7238aab6b7b1dabc8005daa9c900a');
        $url = 'https://api.openweathermap.org/data/2.5/weather';
        
        $city_id = htmlentities(stripslashes($_POST['city_id']));
        $lat = htmlentities(stripslashes($_POST['lat']));
        $lon = htmlentities(stripslashes($_POST['lon']));
        
        $params = "?id=$city_id&appid=".APP_ID."&units=metric";

        $weather_data = get_weather_info($url, $params);

        $response = [];
        if($weather_data['cod'] == 200){

            $response['temp'] = $weather_data['main']['temp'];
            $response['min'] = $weather_data['main']['temp_min'];
            $response['max'] = $weather_data['main']['temp_max'];
            $response['location'] = $weather_data['name'];
            $response['current_date'] = date('D, d M Y H:i', $weather_data['dt']);

            $url = 'https://api.openweathermap.org/data/2.5/onecall';
            $params = '?lat='.$lat.'&lon='.$lon.'&units=metric&exclude=minutely,hourly,alerts&appid='.APP_ID;
            $weekly_data = get_weather_info($url, $params);

            if(isset($weekly_data['daily'])){

                foreach($weekly_data['daily'] as $value){

                    $response['weekly_data'][date('D, d M Y', $value['dt'])]['day'] = $value['temp']['day'];
                    $response['weekly_data'][date('D, d M Y', $value['dt'])]['night'] = $value['temp']['night'];
                }
            }
        }
        else{

            $response['msg'] = "Something Went Wrong!!!";
        }

        echo json_encode($response);
    }

    function get_weather_info($url, $params){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.$params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $weather_data = curl_exec($ch);
        curl_close($ch);

        if($weather_data){

            $weather_data = json_decode($weather_data, true);
            return $weather_data;
        }
    }
?>