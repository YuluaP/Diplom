@extends('welcome')

@section('head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/ScrollableAutocomplete/jquery.ui.autocomplete.scroll.js') }}" defer></script>
    <script src="{{ asset('js/ScrollableAutocomplete/jquery.ui.autocomplete.scroll.min.js') }}" defer></script>
    <style>
        .container{
            padding: 10%;
            text-align: center;
        }
        /* Set the size of the div element that contains the map */
        #map {
            height: 300px;  /* The height is 400 pixels */
            width: 70%;  /* The width is the width of the web page */
            margin: 50px auto;
        }

    </style>

@endsection

@section('content')
    <div class="container">
        <div class="row">
            <!--<div class="col-12"><h2>Поиск маршрута</h2></div>-->
            <div class="col-12">
                <div id="custom-search-input">
                    <div class="input-group">
                        <input type="text" id="search1" name="origin" class="form-control" placeholder="Введите город"/>
                        <input type="hidden" id="search-id1" name="originId" value="{{ old('originId') }}"/>
                    </div><br>
                    <div class="input-group">
                        <input type="text" id="search2" name="destination" class="form-control" placeholder="Введите город"/>
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
                            Название
                            <input type="text" id="directionName" name="directionName" value="{{$directionName}}"/>
                        </label>
                        <label>
                            Комментарий
                            <input type="text" id="directionComment" name="directionComment" value="{{$directionComment}}"/>
                        </label>
                    <label>
                        Личный маршрут
                        @if ($directionPrivate == true)
                            <input type="checkbox" name="private" checked="">
                        @else
                            <input type="checkbox" name="private" >
                        @endif
                    </label>
                        <label>
                        Адрес отправления
                        <input type="text" id="originAddress" name="originAddress" value="{{$originAddress}}" readonly/>
                        </label>
                        <label>
                            Адрес прибытия
                            <input type="text" id="destinationAddress" name="destinationAddress" value="{{$destinationAddress}}" readonly/>
                        </label>
                </div>
                <input type="submit" value="Изменить" name="submit">
            </form>
        </div>
    </div>


    <script>
        var map, marker;
        var myLatlng = {
            lat : parseFloat({{$placeLat}}),
            lng : parseFloat({{$placeLng}})
        };


        console.log(myLatlng.lat, myLatlng.lng);

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
                minLength: 3,
                change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                    }
                },
                select: function( event, ui ) {
                    $("#search-id1").val(ui.item.id);//Put Id in a hidden field
                    $("#originLat").val(ui.item.lat);//Put lat in a field
                    $("#originLng").val(ui.item.lng);//Put lng in a field
                    console.log(ui.item.id, ui.item.value, ui.item.lat, ui.item.lng );
                    myLatlng.lat = parseFloat(ui.item.lat);
                    myLatlng.lng = parseFloat(ui.item.lng);
                    console.log(myLatlng.lat, myLatlng.lng);
                    moveMapOnSelect();

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
                minLength: 3,
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
                    myLatlng.lat = parseFloat(ui.item.lat);
                    myLatlng.lng = parseFloat(ui.item.lng);
                    console.log(myLatlng.lat, myLatlng.lng);
                    moveMapOnSelect();

                },
            });

        });

        function updateCoordinates(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        }


        function moveMapOnSelect() {

            console.log(myLatlng);
            map.setCenter(myLatlng);

            marker.setPosition(myLatlng);
        }

        function initMap() {


            document.getElementById('lat').value = myLatlng.lat;
            document.getElementById('lng').value = myLatlng.lng;

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: myLatlng
            });

            marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                draggable: true
            });

            marker.addListener('dragend', function(e) {
                var position = marker.getPosition();
                var location = marker.getLocation();
                console.log(location);
                updateCoordinates(position.lat(), position.lng())
            });

            map.addListener('click', function(e) {
                marker.setPosition(e.latLng);
                updateCoordinates(e.latLng.lat(), e.latLng.lng())
            });

            map.panTo(myLatlng);


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
