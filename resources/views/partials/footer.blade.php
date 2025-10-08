<!-- Main Theme Js -->
<script src="{{ asset('templates/js/main.js') }}"></script>

<!-- JQuery JS -->
<script src="{{ asset('templates/libs/jquery/dist/jquery.min.js') }}"></script>

<!-- Date & Time Picker JS -->
<script src="{{ asset('templates/libs/moment/moment.js') }}"></script>

<!-- Popper JS -->
<script src="{{ asset('templates/libs/@popperjs/core/umd/popper.min.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('templates/libs/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- Sweetalert JS -->
<script src="{{ asset('templates/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- Custom JS -->
<script src="{{ asset('assets/js/helpers.js') }}"></script>
<script src="{{ asset('assets/js/spa.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<script>
    window.appName = @json(config('app.name'));
    window.routes = {
        login: @json(route('login')),
    }
</script>

<script>
    let navHeight = $(".navbar").outerHeight() + 16;
    $(".main-content").css("margin-top", navHeight + "px");

    let footerHeight = $(".footer").outerHeight() + 16;
    $(".main-content").css("margin-bottom", footerHeight + "px");
</script>

<script>
    let date = moment(new Date());
    $("#year").text(date.format("YYYY"));
</script>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
    @method('POST')
</form>

<script>
    $(document).on("click", "#logout-btn", function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Apakah Anda yakin ingin keluar?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#adb5bd",
            confirmButtonText: "Ya, Keluar",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#logout-form").submit();
            }
        });
    });
</script>

<script>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            showToast('error', '{{ $error }}', 5000);
        @endforeach
    @endif

    @if (session('success'))
        showToast('success', '{{ session('success') }}', 3000);
    @endif

    @if (session('error'))
        showToast('error', '{{ session('error') }}', 3000);
    @endif
</script>
