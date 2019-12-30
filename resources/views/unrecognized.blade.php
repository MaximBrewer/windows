@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    {{ Form::open(array('url' => '/manager/unrecognized', 'method' => 'post')) }}
    {{ Form::submit('Сохранить') }}
    <br><br>
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Марка авто</th>
            <th>Модель авто записать</th>
            <th>Модель авто</th>
            <th>Еврокод</th>
            <th>Ошибка</th>
        </tr>
        @foreach ($unrecognizeds as $unrecognized)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $unrecognized->carProducer->title }}</td>
            <td><input name="unrecognized[{{ $unrecognized->id }}][{{ $unrecognized->carProducer->id }}][]"></td>
            <td>{{ $unrecognized->car_model }}</td>
            <td>{{ $unrecognized->eurocode }}</td>
            <td>{{ $unrecognized->misstake }}</td>
        </tr>
        @endforeach
    </table>
    {{ Form::submit('Сохранить') }}
    {{ Form::close() }}
</div>
@endsection
