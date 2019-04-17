@extends('geo.show')

@section('head')

@endsection

@section('breadcrumbs')
    <li><a href="{{url('/places')}}" class="active">Мои места</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="container">
                <div class="head_main_btn_group">
                    <table class="table table-striped">
                        <tr>
                            <th >Название</th>
                            <th >Адрес</th>
                            <th colspan="2">Доступные действия</th>
                        </tr>
                    @foreach ($places as $place)
                        <tr>
                            <td>{{ $place->place_name }}</td>
                            <td>{{ $place->address }}</td>
                            <td><a class="head_group" id="group_btn_head" href="{{route('places.edit', ['id' => $place->id])}}">Редактировать</a></td>
                            <td><a class="head_private" id="private_btn_head" href="{{route('places.delete', ['id' => $place->id])}}">Удалить</a></td>
                        </tr>
                    @endforeach
                    </table>
                </div>
                {{ $places->links() }}

                <a id="add_btn_head" href="{{url('places/add')}}">Добавить</a>
            </div>
        </div>
    </div>
@endsection
