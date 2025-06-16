<x-app-layout>
   <style>
   .activity-icon {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      color: #fff;
      font-weight: bold;
    }
    .activity-card {
      border-left: 4px solid #0d6efd;
      background-color: #f8f9fa;
    }
    .task-complete {
      text-decoration: line-through;
      color: #6c757d;
    }

   .card{
      color: #000 !important;
   }

   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card mt-0 p-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        {!! trans('panel.complaint.new_title') !!} </h4>
                   

                  </div>
               </div>
            </div>
            <div class="card-body">
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
               @if(session('message_success'))
               <div class="alert alert-success">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <i class="material-icons">close</i>
                  </button>
                  <span>
                     {{ session('message_success') }}
                  </span>
               </div>
               @endif
               @if(session('message_info'))
               <div class="alert alert-info">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <i class="material-icons">close</i>
                  </button>
                  <span>
                     {{ session('message_info') }}
                  </span>
               </div>
               @endif

  
               <!--  -->
               <div class="container-fluid p-4">

                <!--  -->
                 <button type="button" data-toggle="modal" data-target="#updateLeadModel" class="btn btn-primary btn-sm btn-icon-split float-right">
                    <span class="text">Update Leads</span>
                </button>
                <!--  -->
             <!-- Header -->
             <div class="d-flex justify-content-between align-items-center mb-4">
               <div>
                 <h4 class="mb-0">Lemsys Infotech Pvt. Ltd.</h4>
                 <p class="mb-1">H-102, Scheme 74C, Indore, MP 452010</p>
                 <p class="mb-0">
                   📧 <a href="mailto:sales@lemsys.co.in">sales@lemsys.co.in</a> |
                   📞 <a href="tel:+917024801887">+91 7024 801 887</a>
                 </p>
               </div>
               <div>
                 <button class="btn btn-primary me-2">Note</button>
                 <button class="btn btn-outline-primary">Email</button>
               </div>
             </div>

             <!-- Main Content -->
             <div class="row">
               <!-- Left Column: Tasks and Contacts -->
               <div class="col-md-4">
                 <!-- Tasks -->
                 <div class="card mb-4">
                   <div class="card-header d-flex justify-content-between">
                     <strong>Tasks</strong>
                     <a href="#" class="text-decoration-none">+ Add</a>
                   </div>
                   <ul class="list-group list-group-flush">
                     <li class="list-group-item"></li>
                     
                   </ul>
                 </div>

                 <!-- Contacts -->
                 <div class="card">
                   <div class="card-header d-flex justify-content-between">
                     <strong>Contacts</strong>
                     <a href="#" class="text-decoration-none">+ Add</a>
                   </div>
                   <ul class="list-group list-group-flush">
                     @foreach($lead_contacts as $lead_contact)
                     <li class="list-group-item">{{$lead_contact->name??''}}</li>
                     @endforeach
                   </ul>
                   <!--  -->
                   <form method="POST" 
                   action="{{ route('lead-contacts.store') }}" class="form-horizontal" id="frmLeadContactsCreate" enctype="multipart/form-data">
                     @csrf
                   <div class="row">
                     <div class="col-md-6 pr-1 pl-1">
                       <div class="col-md-12 form-group">
                           <label for="name">Contact Name <span style="color:red">*</span></label>
                           <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
                           <input type="text"  name="name" id="name" value="{{ old('name','') }}"  class="form-control" placeholder="Contact Name">
                           @if($errors->has('name'))
                           <p class="help-block">
                               <strong>{{ $errors->first('name') }}</strong>
                           </p>
                           @endif
                       </div>
                     </div>
                     <div class="col-md-6 pr-1 pl-1">
                       <div class="col-md-12 form-group">
                           <label for="title">Title <span style="color:red">*</span></label>
                           
                           <input type="text"  name="title" id="title" value="{{ old('title','') }}"  class="form-control" placeholder="Title">
                           @if($errors->has('title'))
                           <p class="help-block">
                               <strong>{{ $errors->first('title') }}</strong>
                           </p>
                           @endif
                       </div>
                     </div>
                     <div class="col-md-6 pr-1 pl-1">
                       <div class="col-md-12 form-group">
                           <label for="phone_number">Phone Number <span style="color:red">*</span></label>
                           <input type="text"  name="phone_number" id="phone_number" value="{{ old('phone_number','') }}"  class="form-control" placeholder="Phone Number">
                           @if($errors->has('phone_number'))
                           <p class="help-block">
                               <strong>{{ $errors->first('phone_number') }}</strong>
                           </p>
                           @endif
                       </div>     
                     </div>
                     <div class="col-md-6 pr-1 pl-1">
                       <div class="col-md-12 form-group">
                           <label for="contact_email">Email <span style="color:red">*</span></label>
                           <input type="text"  name="contact_email" id="contact_email" value="{{ old('contact_email','') }}"  class="form-control" placeholder="Email">
                           @if($errors->has('contact_email'))
                           <p class="help-block">
                               <strong>{{ $errors->first('contact_email') }}</strong>
                           </p>
                           @endif
                       </div>     
                     </div>
                     <div class="col-md-6 pr-1 pl-1">
                       <div class="col-md-12 form-group">
                           <label for="url">url <span style="color:red">*</span></label>
                           <input type="text"  name="url" id="url" value="{{ old('Url','') }}"  class="form-control" placeholder="Url">
                           @if($errors->has('url'))
                           <p class="help-block">
                               <strong>{{ $errors->first('url') }}</strong>
                           </p>
                           @endif
                       </div>     
                     </div>
                     <div class="col-md-6 pr-1 pl-1">
                       <div class="col-md-12 form-group">
                        <button type="submit" class="btn btn-default">Save</button>
                       </div>
                    </div>
                     
                   </div>
                  </form>
                   <!--  -->
                 </div>

               </div>

               <!-- Right Column: Activities -->
               <div class="col-md-8">
                 <div class="card mb-4">
                   <div class="card-header">
                     <strong>Activities</strong>
                   </div>
                   <div class="card-body">
                     <!-- Note Input -->
                     <div class="mb-3">
                        <form method="POST" 
                          action="{{ route('lead-notes.store') }}" class="form-horizontal" id="frmLeadNotesAdd" enctype="multipart/form-data">
                            @method('POST')
                            @csrf
                            <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
                            {!! Form::textarea('note', old('note',''), ['class' => 'form-control rounded border ckeditor-init', 'placeholder' => 'Add a note...','rows'=>8]) !!}
                           <div class="text-end mt-2">
                             <button class="btn btn-sm btn-primary" type="submit">Done</button>
                           </div>
                        </form>
                     </div>

                     <!-- Activities List -->
                    @php
                        $previousDate = null;
                    @endphp
                    @foreach($lead_notes as $lead_note)
                    @php
                        $noteDate = \Carbon\Carbon::parse($lead_note->created_at)->format('d M Y'); // e.g., 28 Feb 2024
                        $noteTime = \Carbon\Carbon::parse($lead_note->created_at)->format('h:i a'); // e.g., 10:25 pm
                    @endphp
                     <div>
                        @if($noteDate !== $previousDate)
                            <h6 class="text-muted">{{ $noteDate }}</h6>
                            @php
                                $previousDate = $noteDate;
                            @endphp
                        @endif
                       <!-- code 28 feb 2024  -->
                       <div class="card mb-2 p-3 activity-card">
                         <div class="d-flex">
                           <div class="activity-icon bg-success me-3"></div>
                           <div>
                             {!! $lead_note->note??''!!}
                             <div class="text-muted small">{{$noteTime}}</div>
                             <!-- code 10:25 pm  -->
                             <div class="text-muted small">
                                 <form action="{{ route('lead-notes.destroy', $lead_note->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-circle btn-sm">
                                        delete
                                    </button>
                                </form>
                             </div>
                           </div>
                           <div>
                           </div>
                         </div>
                       </div>
                     </div>
                       @endforeach
                   </div>
                 </div>
               </div>
             </div>
           </div>
               <!--  -->

               <div class="card-footer pull-right mt-5">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme','id' => 'submit-btn')) }}
               </div>
               
            </div>
         </div>
      </div>
   </div>


    <div class="modal fade" id="updateLeadModel" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Lead</h4>
        </div>
        
          <form method="POST" 
          action="{{ route('leads.update',$lead) }}" class="form-horizontal" id="frmLeadsUpdate" enctype="multipart/form-data">
          @method('PUT')
            @csrf
          <div class="modal-body">
          <div class="row">
            <div class="col-md-12 pr-1 pl-1">
              <div class="col-md-12 form-group">
                  <label for="company_name">Name <span style="color:red">*</span></label>
                  <input type="text"  name="company_name" id="company_name" value="{{ old('company_name',$lead->company_name??'') }}"  class="form-control" placeholder="Name">
                  @if($errors->has('company_name'))
                  <p class="help-block">
                      <strong>{{ $errors->first('company_name') }}</strong>
                  </p>
                  @endif
              </div>
            </div>
          </div>
        
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Canel</button>
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
      </form>
    </div>
  </div>
   
     <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <!-- Load jQuery and moment.js -->
  <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="{{ url('/').'/'.asset('vendor/ckeditor/js/ckeditor.js') }}"></script>
   <script>

    jQuery(document).ready(function(){

    jQuery('#frmLeadContactsCreate').validate({
        rules: {
            name: {
                required: true
            },
            title: {
                required: true
            },
            phone_number: {
                required: true,
                number: true
            },
            contact_email: {
                required: true,
                email:true
            },
            url: {
                required: true
            },             
        }

    });
    jQuery('#frmLeadsUpdate').validate({
         rules: {
            company_name: {
                required: true
            },
        }
    });

    jQuery('#frmLeadNotesAdd').validate({
        ignore: [],
         rules: {
            note: {
                required: true
            },
        }
    });


    // 1. Initialize CKEditor for all matching elements
    document.querySelectorAll('.ckeditor-init').forEach(function (item) {
        var editor = CKEDITOR.replace(item, {
            customConfig: 'config.js',
            toolbar: 'Basic',
            height: '15em'
        });

        // 2. Update the textarea content on change (already good)
        editor.on('change', function () {
            this.updateElement();
        });
    });


});
   </script>
</x-app-layout>
