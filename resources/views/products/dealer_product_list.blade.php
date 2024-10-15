<x-app-layout>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-icon card-header-theme">
               <div class="card-icon">
                  <i class="material-icons">perm_identity</i>
               </div>
               <h4 class="card-title ">{!! trans('panel.product.title_singular') !!} {!! trans('panel.global.list') !!}
               </h4>
            </div>
            <div class="card-body">
              <div class="main">
               <ul class="cards">
               @foreach($products as $product)   
               @endforeach
            </div>
              </ul>
            </div>
            </div>
            <!-- ----------------------- -->
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
</x-app-layout>