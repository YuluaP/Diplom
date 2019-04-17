@extends('geo.show')

@section('head')

@endsection

@section('breadcrumbs')
    <li><a href="{{url('/directions')}}" class="active">Мои маршруты</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="container">
                <div class="head_main_btn_group">
                <table class="table table-striped">
                    <tr>
                        <th >Название</th>
                        <th >Начало</th>
                        <th >Конец</th>
                        <th >Расстояние</th>
                        <th >Время в пути</th>
                        <th colspan="2">Доступные действия</th>
                        </tr>
                    @foreach ($userDirections as $userDirection)
                        <tr>
                            <td>{{ $userDirection->direction_name }}</td>
                            <td>{{ $userDirection->origin_address }}</td>
                            <td>{{ $userDirection->destination_address }}</td>
                            <td>{{ round($userDirection->distance / 1000, 1, PHP_ROUND_HALF_UP) }} км</td>
                            @if ($userDirection->duration/60 < 60)
                                <td>{{ round($userDirection->duration/60, 0, PHP_ROUND_HALF_UP) }} мин</td>
                            @else
                                <td>{{ round($userDirection->duration/3600, 0, PHP_ROUND_HALF_DOWN) }} ч {{round($userDirection->duration/60 - round($userDirection->duration/3600, 0, PHP_ROUND_HALF_DOWN)*60, 0, PHP_ROUND_HALF_DOWN) }} мин</td>
                            @endif
                            <td><a class="head_group" id="group_btn_head" href="{{route('directions.edit', ['id' => $userDirection->id])}}">Редактировать</a></td>
                            <td><a class="head_private" id="private_btn_head" href="{{route('directions.delete', ['id' => $userDirection->id])}}">Удалить</a></td>
                        </tr>
                    @endforeach
                </table>

                </div>

                {{ $userDirections->links() }}

                <a id="add_btn_head" href="{{url('directions/add')}}">Добавить</a>
            </div>
        </div>
    </div>

@endsection
