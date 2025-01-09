<div class="col-lg-4 col-md-6">
    <div class="card">
        <div class="card-body d-flex align-items-center">
            <div class="h2 mb-0 mr-3">{{ $datas }}</div>
            <div class="flex">
                <div class="card-title text-capitalize">{{ $title }}</div>
                <p class="card-subtitle text-50">
                    {{ $subtitle }}
                        @if($dataThisMonth > 0)
                            {{ $dataThisMonth }}
                            <i class="material-icons text-success icon-16pt">keyboard_arrow_up</i>
                            This Month
                        @elseif($dataThisMonth < 0)
                            {{ $dataThisMonth }}
                            <i class="material-icons text-danger icon-16pt">keyboard_arrow_up</i>
                            This Month
                        @endif
                </p>
            </div>
            <i class='{{ $icon }} icon-24pt text-danger'></i>
        </div>
    </div>
</div>
