@isset($notification)
<div class="alert alert-important alert-success alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ri-information-line me-1"></i>
        </div>
        <div>
            {{ $notification }}
        </div>
    </div>
    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endisset

@isset($success)
<div class="alert alert-important alert-success alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ri-information-line me-1"></i>
        </div>
        <div>
            {{ $success }}
        </div>
    </div>
    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endisset

@isset($warning)
<div class="alert alert-important alert-warning alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ri-information-line me-1"></i>
        </div>
        <div>
            {{ $warning }}
        </div>
    </div>
    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endisset

@isset($error)
<div class="alert alert-important alert-danger alert-dismissible" role="alert">
    <div class="d-flex">
        <div>
            <i class="ri-information-line me-1"></i>
        </div>
        <div>
            {{ $error }}
        </div>
    </div>
    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endisset