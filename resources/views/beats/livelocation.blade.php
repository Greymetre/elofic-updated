<x-app-layout>
<script src="http://maps.google.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18" type="text/javascript"></script>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card mt-4" data-animation="true">
            <div class="card-body">
                @if(session()->has('message_success'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                    </button>
                    <span>
                    {{ session()->get('message_success') }}
                    </span>
                </div>
                @endif
                @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                    </button>
                    <span>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </span>
                </div>
                @endif
                <h5 class="font-weight-normal mt-4">User Live Location</h5>
                <div class="row">
                    <div class="col-md-5">
                        <div class="dropdown bootstrap-select show-tick">
                            <select class="selectpicker" id="user_id" name="user_id" data-style="select-with-transition" title="Choose User" data-size="10" tabindex="-98" >
                              <option disabled=""> Select Users</option>
                              @if(@isset($users ))
                                @foreach($users as $user)
                                  <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                                @endforeach
                              @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group has-default bmd-form-group">
                            <input type="text" class="form-control datepicker" id="date" name="date" placeholder="Select Date" autocomplete="off" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-info btn-sm" onclick="getLocationData()">Location</button>
                        <button class="btn btn-info btn-sm" onclick="getActivityData()">Activity</button>
                    </div>
                </div>
                <div class="row p-3">
                    <div class="col-md-7">
                        <div id="map" style="width: 500px; height: 400px;"></div>
                    </div>
                    <div class="col-md-5">
                        <ul class="timeline timeline-simple" id="todayActivity">
                           
                        </ul>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        // getActivityData();
    })
    function getLocationData(){
        var date = $("input[name=date]").val();
        var user_id = $("select[name=user_id]").val();
        $.ajax({
            url: "{{ url('getUserLocationData') }}",
            dataType: "json",
            type: "POST",
            data:{ _token: "{{csrf_token()}}", date:date,user_id:user_id },
            success: function(res){
                var locations = [];
               $.each(res, function(index,item) {
                     locations.push([
                        item.address + ' '+ item.time, item.latitude,  item.longitude, index+1
                    ])

                });
                var map = new google.maps.Map(document.getElementById('map'), {
                  zoom: 6,
                  center: new google.maps.LatLng('20.5937', '78.9629'),
                  mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                var infowindow = new google.maps.InfoWindow();
                var marker, i;
                for (i = 0; i < locations.length; i++) {  
                  marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map
                  });
                  google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                      infowindow.setContent(locations[i][0]);
                      infowindow.open(map, marker);
                    }
                  })(marker, i));
                }

            }
        });
    }

    function getActivityData(){
        var date = $("input[name=date]").val();
        var user_id = $("select[name=user_id]").val();
        $.ajax({
            url: "{{ url('getUserActivityData') }}",
            dataType: "json",
            type: "POST",
            data:{ _token: "{{csrf_token()}}", date:date,user_id:user_id },
            success: function(res){
                $("#todayActivity").empty();
                if(res.length > 0){
                    $.each(res, function(index,item) {  
                        var classname = 'success';
                        switch(item.title) {
                            case 'Punchin':
                                var classname = 'primary';
                                break;
                            case 'Punchout':
                                var classname = 'warning';
                                break;
                            case 'Checkin':
                                var classname = 'info';
                                break;
                            case 'Checkout':
                                var classname = 'danger';
                                break;
                            case 'Order':
                                var classname = 'success';
                                break;
                            default:
                                var classname = 'default';
                        }
                        if(res.length > 20000){
                        $("#todayActivity").append('<li class="timeline-inverted">'+
                            '<div class="timeline-badge '+classname+'">'+
                                '<i class="material-icons">card_travel</i>'+
                            '</div>'+
                            '<div class="timeline-panel">'+
                                '<div class="timeline-heading">'+
                                    '<span class="badge badge-pill badge-'+classname+'">'+ item.time+'</span>'+
                                '</div>'+
                                '<div class="timeline-body">'+
                                    '<h5 style="font-weight: bold;">'+item.title+'</h5>'+
                                '</div>'+
                                '<h6><i class="ti-time"></i> '+item.msg+'</h6>'+
                            '</div>'+
                        '</li>');
                        }
                    });
                }else{
                    $("#todayActivity").append('<h5 style="font-weight: bold;">No Activity Found</h5>');
                }

            }
        });
    }
  </script>
</x-app-layout>