<!-- Danger Alert -->
{{-- @if ($Msg = Session::get('error'))
    <div class="alert alert-danger alert-top-border alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-3 align-middle fs-16 text-danger"></i><strong> {{ $Msg }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif --}}


<!-- Danger Alert -->
@if ($Msg = Session::get('error'))
    <div class="alert alert-danger alert-dismissible bg-danger text-white alert-label-icon fade show material-shadow"
        role="alert">
        <i class="ri-error-warning-line label-icon"></i><strong>{{ $Msg }}</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-check-line me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif






{{-- 
<!-- Primary Alert -->
<div class="alert alert-primary alert-top-border alert-dismissible fade show" role="alert">
    <i class="ri-user-smile-line me-3 align-middle fs-16 text-primary"></i><strong>Primary</strong> - Top border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Secondary Alert -->
<div class="alert alert-secondary alert-top-border alert-dismissible fade show" role="alert">
    <i class="ri-check-double-line me-3 align-middle fs-16 text-secondary"></i><strong>Secondary</strong> - Top border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Success Alert -->
<div class="alert alert-success alert-top-border alert-dismissible fade show" role="alert">
    <i class="ri-notification-off-line me-3 align-middle fs-16 text-success"></i><strong>Success</strong> - Top border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>





<!-- Warning Alert -->
<div class="alert alert-warning alert-top-border alert-dismissible fade show" role="alert">
    <i class="ri-alert-line me-3 align-middle fs-16 text-warning"></i><strong>Warning</strong> - Top border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Info Alert -->
<div class="alert alert-info alert-top-border alert-dismissible fade show" role="alert">
    <i class="ri-airplay-line me-3 align-middle fs-16 text-info"></i><strong>Info</strong> - Top border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Light Alert -->
<div class="alert alert-light alert-top-border alert-dismissible fade show" role="alert">
    <i class="ri-mail-line me-3 align-middle fs-16 text-light"></i><strong>Light</strong> - Top border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
    
<!-- Dark Alert -->
<div class="alert alert-dark alert-top-border alert-dismissible fade show mb-0" role="alert">
    <i class="ri-refresh-line me-3 align-middle fs-16 text-body"></i><strong>Dark</strong> - Top border alert
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div> --}}


<script>

    // faire disparaitre les alertes apres 10 secondes
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 10000);
</script>