@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row justify-content-start pb-4">
        
        @foreach($images as $image)
            <div class="col-md-4 pb-4">
                <div class="card">
                    <img class="card-img-top" src="{{ $image->url()}}" alt="Image">
                    <div class="card-body">
                        <h5 class="card-title">{{ $image->title }}</h5>
                        <p class="card-text">{{ $image->descriprion }}</p>
                        <p class="card-text">{{ $image->category }}</p>
                        <div class="row">
                            <a href="/images/{{ $image->id }}" class="btn btn-secondary m-1">View</a>
                            <a href="/images/{{ $image->id }}/edit" class="btn btn-primary m-1">Edit</a>
                            <form method="POST" action="/images/{{ $image->id }}">
                                @method('delete')
                                @csrf
                                <button class="btn btn-danger m-1">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row justify-content-center pb-4">
        {{$images->links()}}
    </div>
    <div class="row justify-content-center pb-4">
        <div class="col-md-4">
            <a href="/images/create" class="btn btn-primary btn-block">
                Add Photo
            </a>
        </div>
    </div>
</div>
@endsection