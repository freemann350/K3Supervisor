  <script>
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }

    @if (isset($error_msg))
      toastr.error('{{$error_msg['status']}} ({{$error_msg['code']}})<br>{{$error_msg['message']}}', 'A problem has occurred')
    @endif

    @if (session('success-msg'))
      toastr.success('{{session('success-msg')}}','Success!')
    @endif
  </script>