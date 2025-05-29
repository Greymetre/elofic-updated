<x-app-layout>
   <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
         {{ __('Dashboard') }}
      </h2>
   </x-slot>
   @if(auth()->user()->hasRole('Customer Dealer'))
   <div class="nav-wrapper position-relative end-0">
      <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" id="tabs" role="tablist">
         <li class="nav-item">
            <a class="nav-link active show" data-toggle="tab" href="#sliderTab" role="tablist">
               <i class="material-icons">tune</i> Slider
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#test1" role="tablist">
               <i class="material-icons">check_box_outline_blank</i> Test
            </a>
         </li>
      </ul>
   </div>
   <div class="tab-content tab-space tab-subcategories">
      <div class="tab-pane active show" id="sliderTab">
         @if($dealer_poster_setting->slider == 'Y')
         <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
               @if($dealer_poster_setting->exists && $dealer_poster_setting->getMedia('dealer_portal_slider_image')->count() > 0 && Storage::disk('s3')->exists($dealer_poster_setting->getMedia('dealer_portal_slider_image')[0]->getPath()))
               @foreach($dealer_poster_setting->getMedia('dealer_portal_slider_image') as $k => $media)
               <li data-target="#carouselExampleIndicators" data-slide-to="{{$k}}" class="{{$k==0?'active':''}}"></li>
               @endforeach
               @endif
            </ol>
            <div class="carousel-inner">
               @if($dealer_poster_setting->exists && $dealer_poster_setting->getMedia('dealer_portal_slider_image')->count() > 0 && Storage::disk('s3')->exists($dealer_poster_setting->getMedia('dealer_portal_slider_image')[0]->getPath()))
               @foreach($dealer_poster_setting->getMedia('dealer_portal_slider_image') as $k => $media)
               <div class="carousel-item {{$k==0?'active':''}}">
                  <img class="d-block w-100" src="{{ $media->getFullUrl() }}" alt="{{$media->name}}">
               </div>
               @endforeach
               @endif
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
               <span class="carousel-control-prev-icon" aria-hidden="true"></span>
               <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
               <span class="carousel-control-next-icon" aria-hidden="true"></span>
               <span class="sr-only">Next</span>
            </a>
         </div>
         @endif
      </div>
      <div class="tab-pane active show" id="test1">Second menu</div>
   </div>
   @endif
</x-app-layout>