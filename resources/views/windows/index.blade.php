@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Номенклатура</th>
            <th>Марка авто</th>
            <th>Модель авто</th>
            <th>Кузов авто</th>
            <th>Еврокод</th>
        </tr>
        @foreach ($windows as $window)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $window->title }}</td>
            <td>{{ $window->carModel->carProducer->title }}</td>
            <td>{{ $window->carModel->title }}</td>
            <td>{{ $window->carBody->title }}</td>
            <td>{{ $window->eurocode }}</td>
        </tr>
        @endforeach
    </table>
    {{ $windows->links() }}
</div>
@endsection