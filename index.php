<!DOCTYPE html>
<html>

<head>
    <title>
        Weather Info App
    </title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
		    
        .slideshow {
            position:relative;
            top:20px;
        }
        
        div.slide {
            float:left;
            width: 100px;
            height: 100px;
            opacity:0.7;
            font-size:12px;
            line-height:20px;
            text-align:center;
        }
    </style>
</head>

<body>

    <div id="container" class="container" style="">
        <h2 style="text-align: center;">
            Weather Info App
        </h2>

        <div class="location-search-block">
            <label>Search Location</label>
            <input type="text" id="location" name="location" />
        </div>

        <div id="container2" style="display-inline:block;">
            <p id="current-date"></p>
            <p id="current-temperature"></p>
            <p id="high-low"></p>
            <p id="current-location"></p>
        </div>

        <div class="slideshow">
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var cache = {};

        $(window).on('load', function() {

            //Display the html once everything is loaded
            $('.container').css('display', 'block');
            $('#location').autocomplete({

                minLength: 5,
                delay: 100,
                focus: function(event, ui) {

                    $(".ui-helper-hidden-accessible").hide();
                    event.preventDefault();
                },
                source: function(request, response) {

                    //on enetring the first 5 characters of location search in the json file or get the stored value from cache
                    var term = request.term.toLowerCase();
                    if (term in cache) {

                        response($.map(cache[term], function(item) {
                            return {
                                label: item.city_name,
                                value: item.id,
                                lat: item.lat,
                                lon: item.lon
                            };
                        }));
                        return;
                    }

                    $.ajax({

                        url: 'search-city.php',
                        method: 'GET',
                        data: request,
                        dataType: 'JSON',
                        async: false,
                        success: function(data) {

                            if (data.success) {

                                message = cache[term] = data.data;
                                response($.map(message, function(item) {

                                    if (item) {

                                        obj = {
                                            label: item.city_name,
                                            value: item.id,
                                            lat: item.lat,
                                            lon: item.lon
                                        }

                                        return obj;
                                    }
                                }));
                            }
                        }
                    });
                },
                select: function(event, ui) {

                    //On location selection make a request to the api to get the weather info
                    event.preventDefault();
                    $('#location').val(ui.item.label);

                    data = {
                        'city_id': ui.item.value,
                        'lat': ui.item.lat,
                        'lon': ui.item.lon
                    };

                    $.ajax({

                        url: 'weather-api.php',
                        method: 'POST',
                        data: data,
                        dataType: 'JSON',
                        async: false,
                        success: function(response) {

                            if(response.msg){

                                alert(response.msg);
                            }
                            else{

                                $('#current-date').text(response.current_date);
                                $('#current-temperature').html(response.temp+'&#8451;');
                                $('#high-low').html(response.max+'&#8451;/'+response.min+'&#8451;');
                                $('#current-location').text(response.location);

                                html = '';
                                $.each(response.weekly_data, function(key, value) {
                                    
                                    html += '<div class="slide">';
                                    html += '   <p>'+key+'</p>';
                                    html += '   <p>'+value['day']+'&#8451;/'+value['night']+'&#8451;</p>';
                                    html += '</div>';
                                });

                                $('.slideshow').html(html);
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>