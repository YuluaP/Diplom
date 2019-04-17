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
            Мои маршруты
            <div class="container">
                <table>
                    <tr>
                        <th >Название</th>
                        <th >Начало</th>
                        <th >Конец</th>
                        <th >Расстояние</th>
                        <th >Время в пути</th>
                        <th ></th>
                        <th ></th>
                    </tr>
                    @foreach ($userDirections as $userDirection)
                        <tr>
                            <td>{{ $userDirection->direction_name }}</td>
                            <td>{{ $userDirection->origin_address }}</td>
                            <td>{{ $userDirection->destination_address }}</td>
                            <td>{{ $userDirection->distance }}</td>
                            <td>{{ $userDirection->duration }}</td>
                            <td><a href="{{route('directions.edit', ['id' => $userDirection->id])}}">Редактировать</a></td>
                            <td><a href="{{route('directions.delete', ['id' => $userDirection->id])}}">Удалить</a></td>
                        </tr>
                    @endforeach
                </table>
            </div>

            {{ $userDirections->links() }}

            <input id="add-new" type="submit" value="Добавить" name="submit">
        </div>
    </div>


    <script>
        document.getElementById('add-new').addEventListener('click', function () {
            //window.location.href = 'http://way24.loc/add-place';
            window.location.href = "{{url('directions/add')}}";
        });
    </script>


@endsection
