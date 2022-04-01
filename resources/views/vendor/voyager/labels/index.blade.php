@extends('voyager::master')
@section('content')

    <div class="labels-container">
        <h1 class="labels-title">Labels:</h1>
        <form action="{{ route('voyager_labels_edit') }}" method="POST">
        @csrf <!-- {{ csrf_field() }} -->
            @foreach($locales as $key => $value)
                <h2 class="labels-title">{{$key}}</h2>
                <div class="labels-wrapper">
                    @if(is_array($value))
                        @foreach($value as $nested_key => $nested_value)
                            <label for="{{$key}}[{{$nested_key}}]{{$nested_value}}">{{$nested_key}}</label>
                            <input type="hidden" name="{{$key}}[{{$nested_key}}]" placeholder="{{$nested_key}}">
                            <input class="labels-input" type="text" name="{{$key}}[{{$nested_key}}]{{$nested_value}}" placeholder="{{$nested_value}}">
                        @endforeach
                    @else
                        <label for="{{$key}}[first_level]{{$value}}]">{{$key}}</label>
                        <input type="hidden" name="{{$key}}[first_level][{{$value}}]" value="{{$key}}">
                        <input class="labels-input" type="text"  name="{{$key}}[first_level]{{$value}}]"  value="{{$value}}">
                    @endif
                </div>
            @endforeach
            <input class="labels-button" type="submit" name="submit" id="">
        </form>
    </div>
@stop
