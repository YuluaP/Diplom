@extends('welcome')

@section('head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">


@endsection

@section('content')
    <div class="container">
        <div class="row">
            мои места
            <div class="container">
                <table>
                    <tr>
                        <th >Название</th>
                        <th >Адрес</th>
                        <th ></th>
                        <th ></th>
                    </tr>
                @foreach ($places as $place)
                    <tr>
                        <td>{{ $place->place_name }}</td>
                        <td>{{ $place->address }}</td>
                        <td><a href="{{route('places.edit', ['id' => $place->id])}}">Редактировать</a></td>
                        <td><a href="{{route('places.delete', ['id' => $place->id])}}">Удалить</a></td>
                    </tr>
                @endforeach
                </table>
            </div>

            {{ $places->links() }}

            <input id="add-new" type="submit" value="Добавить" name="submit">
        </div>
    </div>


    <script>
        document.getElementById('add-new').addEventListener('click', function () {
            //window.location.href = 'http://way24.loc/add-place';
            window.location.href = "{{url('places/add')}}";
        });
    </script>


@endsection
