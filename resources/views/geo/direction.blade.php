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

    </style>

@endsection

@section('content')
<form method="POST">
    @csrf
    <div class="container">
        <div class="row">
            <div class="col-12"><h2>Поиск маршрута</h2></div>
            <div class="col-12">
                <div id="custom-search-input">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="input-group">
                        <label>Местоположение: </label><input id="search1" name="origin" value="{{ $origin }}" type="text" class="form-control" placeholder="Введите местоположение"/>
                        <input type="hidden" id="search-id1" name="originId" value="{{ $originId }}"/>
                    </div><br>
                    <div class="input-group">
                        <label>Место назначения:</label><input id="search2" name="destination" value="{{ $destination }}" type="text" class="form-control" placeholder="Введите место назначения" />
                        <input type="hidden" id="search-id2" name="destinationId" value="{{ $destinationId}}"/>
                    </div><br>
                    <input url="{{url('direction')}}" type="submit" value="Рассчитать расстояние и время" name="submit">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div id="result">
                        <br><label><b>Расстояние: </b></label> <span>{{$directionDistance1}} км</span><br>
                        <label><b>Время в пути: </b></label> <span>{{$directionDuration1}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $( "#search1" ).autocomplete({
            maxShowItems: 5,
            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        response($.map(data, function (obj) {
                            return {
                                id: obj.id,
                                value: obj.name
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
                console.log(ui.item.id, ui.item.value );
            },
        });

        $( "#search2" ).autocomplete({
            maxShowItems: 5,
            source: function(request, response) {
                $.ajax({
                    url: "{{url('autocomplete')}}",
                    data: {
                        term : request.term
                    },
                    dataType: "json",
                    success: function(data){
                        response($.map(data, function (obj) {
                            return {
                                id: obj.id,
                                value: obj.name
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
                console.log(ui.item.id, ui.item.value );
            },
        });

    });

</script>
@endsection
