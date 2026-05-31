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
<script>
class SimpleSearchableSelect {
    constructor(selector) {
        this.elements = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        this.elements.forEach(select => {
            this.createUI(select);
        });
    }

    createUI(select) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('searchable-wrapper');

        const input = document.createElement('input');
        input.type = 'text';
        input.classList.add('searchable-input');
        input.placeholder = 'Search...';
        input.readOnly = false;

        const dropdown = document.createElement('div');
        dropdown.classList.add('searchable-dropdown');

        const options = Array.from(select.options);

        const buildDropdown = (filter = '') => {
            dropdown.innerHTML = '';

            options.forEach(option => {
                if (!option.value) return;

                if (
                    option.text.toLowerCase().includes(filter.toLowerCase())
                ) {
                    const item = document.createElement('div');
                    item.classList.add('searchable-item');
                    item.textContent = option.text;
                    item.dataset.value = option.value;

                    item.addEventListener('click', () => {
                        select.value = option.value;
                        input.value = option.text;
                        dropdown.style.display = 'none';

                        select.dispatchEvent(new Event('change'));
                    });

                    dropdown.appendChild(item);
                }
            });
        };

        input.addEventListener('focus', () => {
            dropdown.style.display = 'block';
            buildDropdown();
        });

        input.addEventListener('input', (e) => {
            buildDropdown(e.target.value);
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        wrapper.appendChild(input);
        wrapper.appendChild(dropdown);

        select.style.display = 'none';
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        // preload selected value
        if (select.value) {
            const selectedText = select.options[select.selectedIndex].text;
            input.value = selectedText;
        }
    }
}

// INIT
document.addEventListener('DOMContentLoaded', () => {
    new SimpleSearchableSelect('.js-searchable');
});
</script>