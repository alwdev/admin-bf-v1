@extends('layouts.guest')
@section('styles')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">เพิ่มสมุดบัญชี</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                    <li class="breadcrumb-item active">เพิ่มสมุดบัญชี</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="active tab-pane" id="settings">
                    @if (session('success'))
                          <div class="alert alert-success">
                              {{ session('success') }}
                          </div>
                      @endif
                    @if($errors)
                      @foreach ($errors->all() as $error)
                          <div class="alert alert-danger">{{ $error }}</div>
                      @endforeach
                  @endif
                 
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">โลโก้</label>
                      <div class="col-sm-10">
                        <form action="#" method="POST" id="img-upload" enctype="multipart/form-data">
                          @csrf
          
                          <input type="file" id="storeimgupload" name="storeimgupload" style="display:none"/> 
                            <img class="profile-user-img img-fluid img-circle" style="max-width: 150px;" src="@if(isset($store->image)){{ asset($store->image) }}?{{ rand() }} @else /dist/img/photo1.png @endif " alt="Store profile picture" id="storeimage">
                        </form>
                      </div>
                    </div>
                    <form method="POST" action="/storeconfigure" enctype="multipart/form-data"  class="form-horizontal"  id="">
                    @csrf
                    <div class="form-group row">
                      <label for="merchantID" class="col-sm-2 col-form-label">ชื่อบริษัท</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control @error('merchantID') is-invalid @enderror" id="merchantID" name="merchantID" placeholder="merchantID" value="{{ $store->merchantID }}" disabled required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="name" class="col-sm-2 col-form-label">ชื่อบริษัท</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="ชื่อบริษัท" value="{{ $store->name }}" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="website" class="col-sm-2 col-form-label">เว็บไซต์</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website" placeholder="เว็บไซต์" value="{{ $store->website }}" >
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="domain_name" class="col-sm-2 col-form-label">ชื่อโดเมน</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control @error('domain_name') is-invalid @enderror" id="domain_name" name="domain_name" placeholder="ชื่อโดเมน" value="{{ $store->domain }}" >
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-sm-2 col-form-label">email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="อีเมล" value="{{ $store->email }}">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="phone1" class="col-sm-2 col-form-label">เบอร์โทรศัพท์</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="phone1"  name="phone1" placeholder="เบอร์โทรศัพท์" value="{{ $store->phone1 }}">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="address" class="col-sm-2 col-form-label">ที่อยู่</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name="address" placeholder="ที่อยู่" value="{{ $store->address1 }}">
                      </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                      <label for="secret_key" class="col-sm-2 col-form-label">Secret Key</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="secret_key" name="secret_key" placeholder="Secret Key" value="{{ $store->secret_key }}">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="API_key" class="col-sm-2 col-form-label">API key</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="API_key" name="API_key" placeholder="API key" value="{{ $store->API_key }}">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="auth_key" class="col-sm-2 col-form-label">Auth Key</label>
                      <div class="col-sm-10">
                        <textarea class="form-control" name="auth_key" id="auth_key" cols="30" rows="10">{{ $store->auth_key }}</textarea>
                      </div>
                    </div>

                    {{-- <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">City</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputcity" name="inputcity"  placeholder="City Name" value="{{ $store->city }}">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">State</label>
                      <div class="col-sm-10">

                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">Country</label>
                      <div class="col-sm-10">

                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputName2" class="col-sm-2 col-form-label">Zip Code</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputzipcode" name="inputzipcode" placeholder="Zip Code" value="{{ $store->zipcode }}">
                      </div>
                    </div> --}}
                    
                    <div class="form-group row">
                      <div class="offset-sm-2 col-sm-10">
                        <button type="submit" name="configsavebtn" class="btn btn-success">Save</button>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
            <!-- end card-body -->
        </div>
    </div> <!-- end col-->
</div> <!-- end row -->
@endsection
@section('scripts')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>


<script type="text/javascript">
  $(function() {
    var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
  });
</script>

<script type="text/javascript">
  $('#storeimage').click(function(){
     $('#storeimgupload').trigger('click'); 
    });

    $('#storeimgupload').change(function(e){
      e.preventDefault();
      const fsize = (this.files[0].size)/1024;
      const fname = ((this.files[0].name).split('.').pop()).toLowerCase();
      
      if( fname!="png" && fname!="jpg" && fname!="jpeg"){
        return AlertNoConfirm('error','Wrong File!');
      }
      else if(fsize>2048){
        return AlertNoConfirm('error','Image are too large!');
      }
      else{
        $('#img-upload').submit();
      }
     
    });

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $('#img-upload').submit(function(e) {
      e.preventDefault();
      let formData = new FormData(this);
      $('#file-input-error').text('');
      $.ajax({
          type:'POST',
          url: "/uploadstoreimage",
          data: formData,
          contentType: false,
          processData: false,
          success: (response) => {
            $('.save-wrapper').removeClass('show');
              if (response) {
                  this.reset();
                  
                  //alert('File has been uploaded successfully');
                  console.log(response);
                  //$('#userimage').attr('src','');
                  var  imgname= '/'+response+'?'+Math.random();
                  $('#storeimage').attr('src',imgname);
                  $('#shoplogo').attr('src',imgname);
                  $(function() {
                    var Toast = Swal.mixin({
                      toast: true,
                      position: 'top-end',
                      showConfirmButton: false,
                      timer: 3000
                    });
                    Toast.fire({
                      icon: 'success',
                      title: 'File has been uploaded successfully.'
                    });
                  });
              }
          },
          error: function(response){
              $('#file-input-error').text(response.responseJSON.message);
              $('.save-wrapper').removeClass('show');
          }
     });
  });

</script>
 <script type="text/javascript">
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  }); 
  $('button#changemethod').click(function(e) {
      e.preventDefault();
      
      $.ajax({
          type:'POST',
          url: "/storeconfigure/changecostmethod",
          //data: ,
          contentType: false,
          processData: false,
          success: (response) => {
              if(response.success=== true) {
                 //console.log(response.text);
                 successalertbox(response.text,"/storeconfigure");
              }
              else if(response.success=== false){
                //console.log(response.text);
                erroralertbox(response.text);
              }
              $('.save-wrapper').removeClass('show');
          },
          error: function(response){
             $('.save-wrapper').removeClass('show');
          }
     });
  });

  $('button#request_ach_payment').click(function(e) {
      e.preventDefault();
      
      $.ajax({
          type:'POST',
          url: "/storeconfigure/request_ach_payment",
          //data: ,
          contentType: false,
          processData: false,
          success: (response) => {
              if(response.success=== true) {
                 //console.log(response.text);
                 successalertbox(response.text,"/storeconfigure");
              }
              else if(response.success=== false){
                //console.log(response.text);
                erroralertbox(response.text);
              }
              $('.save-wrapper').removeClass('show');
          },
          error: function(response){
             $('.save-wrapper').removeClass('show');
          }
     });
  });
  $('button#request_creditCard_payment').click(function(e) {
      e.preventDefault();
      
      $.ajax({
          type:'POST',
          url: "/storeconfigure/request_creditCard_payment",
          //data: ,
          contentType: false,
          processData: false,
          success: (response) => {
              if(response.success=== true) {
                 console.log(response.text);
                 successalertbox(response.text,"/storeconfigure");
              }
              else if(response.success=== false){
                //console.log(response.text);
                erroralertbox(response.text);
              }
              $('.save-wrapper').removeClass('show');
          },
          error: function(response){
             $('.save-wrapper').removeClass('show');
          }
     });
  });
  $('button#request_terminal_payment').click(function(e) {
      e.preventDefault();
      
      $.ajax({
          type:'POST',
          url: "/storeconfigure/request_terminal_payment",
          //data: ,
          contentType: false,
          processData: false,
          success: (response) => {
              if(response.success=== true) {
                 //console.log(response.text);
                 successalertbox(response.text,"/storeconfigure");
              }
              else if(response.success=== false){
                //console.log(response.text);
                erroralertbox(response.text);
              }
              $('.save-wrapper').removeClass('show');
          },
          error: function(response){
             $('.save-wrapper').removeClass('show');
          }
     });
  });
  function disable_payment(paymenttype){
    $.ajax({
          type:'POST',
          url: "/storeconfigure/"+paymenttype,
          //data: ,
          contentType: false,
          processData: false,
          success: (response) => {
              if(response.success=== true) {
                 //console.log(response.text);
                 successalertbox(response.text,"/storeconfigure");
              }
              else if(response.success=== false){
                //console.log(response.text);
                erroralertbox(response.text);
              }
              $('.save-wrapper').removeClass('show');
          },
          error: function(response){
             $('.save-wrapper').removeClass('show');
          }
     });
  }
</script>
@endsection
