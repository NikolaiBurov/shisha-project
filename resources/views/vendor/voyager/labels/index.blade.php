@extends('voyager::master')
@section('content')

    <h1>Labels:</h1>

    <form action="{{ route('voyager_labels_edit') }}" method="POST">
    @csrf <!-- {{ csrf_field() }} -->

        @foreach($locales as $key => $value)
            @if(is_array($value))
                <h2>{{$key}}</h2>
                @foreach($value as $nested_key => $nested_value)
                    <div>
                        <input type="hidden" name="{{$key}}[{{$nested_key}}]" value="{{$nested_key}}">{{$nested_key}} :
                        <input type="text" name="{{$key}}[{{$nested_key}}]{{$nested_value}}" value="{{$nested_value}}">
                    </div>
                @endforeach
            @else
                <div>
                    <input type="hidden" name="{{$key}}[first_level][{{$value}}]" value="{{$key}}">{{$key}} :
                    <input type="text"  name="{{$key}}[first_level]{{$value}}]"  value="{{$value}}">
                </div>
            @endif
        @endforeach
        <input type="submit" name="submit" id="">
    </form>
@stop
