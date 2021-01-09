@extends('layouts.app')
@section('content')


    @foreach($posts as $post)
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">


                    <img class="img-thumbnail" width="50" src="public/images/{{$post->user->picUrl}}"><span
                            class="card-title">{{$post->user->name}}</span>
                    <a href="deletepicture/{{$post->id}}" class="align-content-end">
                        <button class="btn btn-danger">Delete</button>
                    </a>


                </div>
                <div class="card-body">
                    @if($post->post_type=='image')
                        <img src="public/images/{{$post->images_url}}" width="450">
                    @elseif($post->post_type=='multi')
                        @foreach($post->pictures as $pic)
                            <img src="public/images/{{$pic}}" width="200">
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <br>

        {{--<div class="slideshow-container">--}}

        {{--<!-- Full-width images with number and caption text -->--}}
        {{--<div class="mySlides fade">--}}
        {{--<div class="numbertext">1 / 3</div>--}}
        {{--<img src="img1.jpg" style="width:100%">--}}
        {{--<div class="text">Caption Text</div>--}}
        {{--</div>--}}

        {{--<div class="mySlides fade">--}}
        {{--<div class="numbertext">2 / 3</div>--}}
        {{--<img src="img2.jpg" style="width:100%">--}}
        {{--<div class="text">Caption Two</div>--}}
        {{--</div>--}}

        {{--<div class="mySlides fade">--}}
        {{--<div class="numbertext">3 / 3</div>--}}
        {{--<img src="img3.jpg" style="width:100%">--}}
        {{--<div class="text">Caption Three</div>--}}
        {{--</div>--}}

        {{--<!-- Next and previous buttons -->--}}
        {{--<a class="prev" onclick="plusSlides(-1)">&#10094;</a>--}}
        {{--<a class="next" onclick="plusSlides(1)">&#10095;</a>--}}
        {{--</div>--}}
        {{--<br>--}}

        {{--<!-- The dots/circles -->--}}
        {{--<div style="text-align:center">--}}
        {{--<span class="dot" onclick="currentSlide(1)"></span>--}}
        {{--<span class="dot" onclick="currentSlide(2)"></span>--}}
        {{--<span class="dot" onclick="currentSlide(3)"></span>--}}
        {{--</div>--}}


    @endforeach

@stop
