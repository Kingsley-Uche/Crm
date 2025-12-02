<!-- Right bar overlay -->
<div class="rightbar-overlay"></div>

<!-- JAVASCRIPT -->

<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
     
</script>
<!-- Core Libraries -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- Charts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>

<!-- DataTables Core -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<!-- DataTables Buttons -->
<script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/libs/jszip/jszip.min.js')}}"></script>
<script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
<script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js')}}"></script> <!-- 🔹 Needed for PDF export -->
<script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>

<!-- DataTables Responsive -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

<!-- SweetAlert -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- DataTables Init -->
<script src="{{ asset('assets/js/pages/datatables.init.js')}}"></script>

<!-- App -->
<script src="{{ asset('assets/js/app.js') }}"></script>


<!-- Optional custom script -->
<script>
  // Your custom JS code here


 $(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();

        const form = $(this).closest('form');
        const ownerFName = $(this).data('fname');
        const ownerLName = $(this).data('lname');

        Swal.fire({
            title: 'Are you sure?',
            text: `Are you sure you? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#79d114',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, !'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                form.submit();
            }
        });
    });
});

</script>
<script>
    @section('script')

@endsection

</script>
