@extends('geo.show')

@section('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>

        /* Set the size of the div element that contains the map */
        #map {
            height: 300px;  /* The height is 400 pixels */
            width: 70%;  /* The width is the width of the web page */
            margin: 50px auto;
        }
        #originAddress, #destinationAddress {
            width: 500px; /* Ширина поля в пикселах */
        }
        #directionName {
            width: 770px; /* Ширина поля в пикселах */
        }

    </style>
@endsection

@section('breadcrumbs')
    <li><a href="{{url('/directions')}}">Мои маршруты</a></li>
    <li><a href="{{url('/directions/{id}/edit')}}" class="active">Редактировать</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div id="custom-search-input">
                    <div class="input-group">
                        <input type="text" id="search1" name="origin" class="form-control" placeholder="Выберите место отправления"/>
                        <input type="hidden" id="search-id1" name="originId" value="{{ old('originId') }}"/>
                        <input type="text" id="search2" name="destination" class="form-control" placeholder="Выберите место прибытия"/>
                        <input type="hidden" id="search-id2" name="destinationId" value="{{ old('destinationId') }}"/>
                    </div><br>
                </div>
            </div>

            <!--The div element for the map -->
            <div id="map"></div>

            <form method="POST" action="{{route('directions.update')}}">
                @csrf
                <div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <label>
                        <!--directionId-->
                        <input type="hidden" id="directionId" name="directionId" value="{{$directionId}}"/>
                    </label>
                    <label>
                        <!--Широта-->
                        <input type="hidden" id="originLat" name="originLat" value="{{$originLat}}"/>
                    </label>
                    <label>
                        <!--Долгота-->
                        <input type="hidden" id="originLng" name="originLng" value="{{$originLng}}"/>
                    </label>
                    <label>
                        <!--Широта-->
                        <input type="hidden" id="destinationLat" name="destinationLat" value="{{$destinationLat}}"/>
                    </label>
                    <label>
                        <!--Долгота-->
                        <input type="hidden" id="destinationLng" name="destinationLng" value="{{$destinationLng}}"/>
                    </label>

                    <label>
                        Адрес отправления<br/>
                        <input type="text" id="origin_address" name="origin_address" value="{{$originAddress}}" readonly/>
                    </label>
                    <label>
                        Адрес прибытия<br/>
                        <input type="text" id="destinationAddress" name="destinationAddress" value="{{$destinationAddress}}" readonly/>
                    </label><br/>
                    <label>
                            Название
                            <input type="text" id="directionName" name="directionName" value="{{$directionName}}"/>
                    </label>
                    <label>
                        Личный маршрут
                        @if ($directionPrivate == true)
                            <input type="checkbox" name="private" checked="">
                        @else
                            <input type="checkbox" name="private" >
                        @endif
                    </label><br/>
                    <label>
                        Комментарий
                        <input type="text" id="directionComment" name="directionComment" value="{{$directionComment}}"/>
                    </label>
                    <label>
                        <p>Расстояние: <span id="distance"></span></p>
                        <p>Время в пути: <span id="duration"></span></p>
                    </label>
                    <input type="hidden" id="clearDistance" name="clearDistance" value="{{$clearDistance}}" readonly/>
                    <input type="hidden" id="clearDuration" name="clearDuration" value="{{$clearDuration}}" readonly/>
                    <label>
                        <input type="submit" id="submit" value="Изменить" name="submit">
                    </label>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/ScrollableAutocomplete/jquery.ui.autocomplete.scroll.js') }}" defer></script>
    <script src="{{ asset('js/ScrollableAutocomplete/jquery.ui.autocomplete.scroll.min.js') }}" defer></script>

    <script>
        var map, marker;
        var originLatlng = {
            lat : parseFloat({{$originLat}}),
            lng : parseFloat({{$originLng}})
        };

        var destinationLatlng = {
            lat : parseFloat({{$destinationLat}}),
            lng : parseFloat({{$destinationLng}})
        };

        //console.log(myLatlng.lat, myLatlng.lng);

        $(document).ready(function() {


            $( "#search1" ).autocomplete({
                maxShowItems: 5,
                source: function(request, response) {
                    $.ajax({
                        url: "{{url('directions/search')}}",
                        data: {
                            term : request.term
                        },
                        dataType: "json",
                        success: function(data){
                            response($.map(data, function (obj) {
                                return {
                                    id: obj.id,
                                    value: obj.name,
                                    lat: obj.lat,
                                    lng: obj.lng
                                }
                            }))

                        }
                    });
                },
                minLength: 1,
                change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                    }
                },
                select: function( event, ui ) {
                    $("#search-id1").val(ui.item.id);//Put Id in a hidden field
                    $("#originLat").val(ui.item.lat);//Put lat in a field
                    $("#originLng").val(ui.item.lng);//Put lng in a field
                    //console.log(ui.item.id, ui.item.value, ui.item.lat, ui.item.lng );
                    originLatlng.lat = parseFloat(ui.item.lat);
                    originLatlng.lng = parseFloat(ui.item.lng);
                    //console.log(myLatlng.lat, myLatlng.lng);
                    //moveMapOnSelect();

                },
            });

            $( "#search2" ).autocomplete({
                maxShowItems: 5,
                source: function(request, response) {
                    $.ajax({
                        url: "{{url('directions/search')}}",
                        data: {
                            term : request.term
                        },
                        dataType: "json",
                        success: function(data){
                            response($.map(data, function (obj) {
                                return {
                                    id: obj.id,
                                    value: obj.name,
                                    lat: obj.lat,
                                    lng: obj.lng
                                }
                            }))

                        }
                    });
                },
                minLength: 1,
                change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                    }
                },
                select: function( event, ui ) {
                    $("#search-id2").val(ui.item.id);//Put Id in a hidden field
                    $("#destinationLat").val(ui.item.lat);//Put lat in a field
                    $("#destinationLng").val(ui.item.lng);//Put lng in a field
                    console.log(ui.item.id, ui.item.value, ui.item.lat, ui.item.lng );
                    destinationLatlng.lat = parseFloat(ui.item.lat);
                    destinationLatlng.lng = parseFloat(ui.item.lng);
                    //console.log(myLatlng.lat, myLatlng.lng);
                    //moveMapOnSelect();
                    getDirection();
                },
            });
        });

        /*function updateCoordinates(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        }

        function moveMapOnSelect() {

            console.log(myLatlng);
            map.setCenter(myLatlng);
            marker.setPosition(myLatlng);
        }*/

        function initMap() {

            getDirection();


        }

        function getDirection() {

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: originLatlng
            });

            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer({
                //draggable: true,
                map: map,
                panel: document.getElementById('right-panel')
            });

            directionsDisplay.addListener('directions_changed', function() {
                computeTotalDistance(directionsDisplay.getDirections());
            });

            displayRoute(originLatlng.lat + ',' + originLatlng.lng, destinationLatlng.lat + ',' + destinationLatlng.lng,
                directionsService, directionsDisplay);

            //displayRoute('46.95372287,31.99470681', '46.98460011,31.99842095',
            //  directionsService, directionsDisplay)

            getFormattedOriginAddress();
            getFormattedDestinationAddress();
        }


        function displayRoute(origin, destination, service, display) {
            service.route({
                origin: origin,
                destination: destination,
                //waypoints: [{location: 'Adelaide, SA'}, {location: 'Broken Hill, NSW'}],
                travelMode: 'DRIVING',
                avoidTolls: true
            }, function(response, status) {
                if (status === 'OK') {
                    display.setDirections(response);
                } else {
                    alert('Could not display directions due to: ' + status);
                }
            });
        }

        function getFormattedOriginAddress() {

            var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + originLatlng.lat + "," + originLatlng.lng + "&sensor=false&key=AIzaSyBRupxmv_FiFu_vArjYd6em98mTvowpjLU";
            //console.log(url);
            $.getJSON(url, function (data) {
                /*for(var i=0;i<data.results.length;i++) {
                    var adress = data.results[i].formatted_address;
                    console.log(adress);
                    }*/

                var originAddress = data.results[0].formatted_address;
                console.log(originAddress);

                document.getElementById('origin_address').value = originAddress;
            });
        }

        function getFormattedDestinationAddress() {

            var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + destinationLatlng.lat + "," + destinationLatlng.lng + "&sensor=false&key=AIzaSyBRupxmv_FiFu_vArjYd6em98mTvowpjLU";
            //console.log(url);
            $.getJSON(url, function (data) {
                /*for(var i=0;i<data.results.length;i++) {
                    var adress = data.results[i].formatted_address;
                    console.log(adress);
                    }*/

                var destinationAddress = data.results[0].formatted_address;
                console.log(destinationAddress);

                document.getElementById('destinationAddress').value = destinationAddress;
            });
        }

        function computeTotalDistance(result) {
            var distance = 0;
            var duration = 0;
            var myroute = result.routes[0];
            for (var i = 0; i < myroute.legs.length; i++) {
                distance += myroute.legs[i].distance.value;
                duration += myroute.legs[i].duration.value;
            }
            document.getElementById('clearDistance').value = distance;
            document.getElementById('clearDuration').value = duration;
            distance = distance / 1000;
            document.getElementById('distance').innerHTML = distance.toFixed(1) + ' км';
            if (duration / 60 < 60){
                document.getElementById('duration').innerHTML = Math.round(duration/60) + ' мин';
            }else {
                document.getElementById('duration').innerHTML = Math.floor(duration/3600) + ' ч ' + Math.round(duration/60 - Math.floor(duration/3600)*60)  + ' мин';
            }

        }



    </script>

    <!-- Источник: https://ru.stackoverflow.com/questions/787724/%D0%9A%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%82%D1%8B-%D0%BC%D0%B5%D1%82%D0%BA%D0%B8-%D0%B8%D0%B7-google-maps -->

    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRupxmv_FiFu_vArjYd6em98mTvowpjLU&callback=initMap">
    </script>
@endsection
