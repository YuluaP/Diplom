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
    <li><a href="{{url('/places')}}">Мои места</a></li>
    <li><a href="{{url('/places/{id}/edit')}}" class="active">Редактировать</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <!--<div class="col-12"><h2>Поиск маршрута</h2></div>-->
            <div class="col-12">
                <div id="custom-search-input">
                    <div class="input-group">
                        <input type="text" id="search1" name="locality" class="form-control" placeholder="Введите город"/>
                        <input type="hidden" id="search-id1" name="localityId" value="{{ old('localityId') }}"/>
                    </div><br>
                </div>
            </div>


            <!--The div element for the map -->
            <div id="map"></div>

            <form method="POST" action="{{route('places.update')}}">
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
                        <!--placeId-->
                        <input type="hidden" id="placeId" name="placeId" value="{{$placeId}}"/>
                    </label>
                    <label>
                        <!--Широта-->
                        <input type="hidden" id="lat" name="lat" value="{{$placeLat}}"/>
                    </label>
                    <label>
                        <!--Долгота-->
                        <input type="hidden" id="lng" name="lng" value="{{$placeLng}}"/>
                    </label>
                    <label>
                        Название
                        <input type="text" id="placeName" name="placeName" value="{{$placeName}}"/>
                    </label>
                    <label>
                        Комментарий
                        <input type="text" id="placeComment" name="placeComment" value="{{$placeComment}}"/>
                    </label>
                    <label>
                        Личное место
                        @if ($placePrivate == true)
                            <input type="checkbox" name="private" checked="">
                        @else
                            <input type="checkbox" name="private" >
                        @endif
                    </label>
                    <label>
                        Адрес
                        <input type="text" id="placeAddress" name="placeAddress" value="{{$placeAddress}}" readonly/>
                    </label>
                </div>
                <input type="submit" id="submit" value="Изменить" name="submit">
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
        var myLatlng = {
            lat : parseFloat({{$placeLat}}),
            lng : parseFloat({{$placeLng}})
        };


        //console.log(myLatlng.lat, myLatlng.lng);

        $(document).ready(function() {
            $( "#search1" ).autocomplete({
                maxShowItems: 5,
                source: function(request, response) {
                    $.ajax({
                        url: "{{url('places/search')}}",
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
                    $("#lat").val(ui.item.lat);//Put lat in a field
                    $("#lng").val(ui.item.lng);//Put lng in a field
                    //console.log(ui.item.id, ui.item.value, ui.item.lat, ui.item.lng );
                    myLatlng.lat = parseFloat(ui.item.lat);
                    myLatlng.lng = parseFloat(ui.item.lng);
                    //console.log(myLatlng.lat, myLatlng.lng);
                    moveMapOnSelect();
                },
            });
        });

        function updateCoordinates(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            myLatlng.lat = lat;
            myLatlng.lng = lng;
            getFormattedAddress();
        }

        function moveMapOnSelect() {

            console.log(myLatlng);
            map.setCenter(myLatlng);
            marker.setPosition(myLatlng);
            getFormattedAddress();
        }

        function getFormattedAddress() {

            var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + myLatlng.lat + "," + myLatlng.lng + "&sensor=false&key=AIzaSyBRupxmv_FiFu_vArjYd6em98mTvowpjLU";
            //console.log(url);
            $.getJSON(url, function (data) {
                /*for(var i=0;i<data.results.length;i++) {
                    var adress = data.results[i].formatted_address;
                    console.log(adress);
                    }*/
                var address = data.results[0].formatted_address;
                console.log(address);
                document.getElementById('placeAddress').value = address;
            });
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
