 
    <script src="{{asset('toastr/toastr.min.js')}} "></script>
 
    <link rel="stylesheet" href="{{asset('toastr/toastr.min.css')}}">


<script>
@if(Session::has('success'))


            toastr.options = {
               "preventDuplicates": true,
               "preventOpenDuplicates": true
            };
            toastr.success("{{ Session::get('success') }}");
            toastr.options.closeButton = true;
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.options.timeOut = 300000;
            
@endif


@if(Session::has('error'))



            toastr.options = {
               "preventDuplicates": true,
               "preventOpenDuplicates": true
            };
            toastr.error("{{ Session::get('error') }}");
            toastr.options.closeButton = true;
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.options.timeOut = 300000;
  
@endif
</script>
