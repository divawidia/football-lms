<div class="col-6 col-lg-4">
    <div class="card border-1 border-left-3 border-left-accent">
        <div class="card-body d-flex align-items-center">
            <div class="h2 mb-0 mr-3">{{ $data }}</div>
            <div class="ml-auto text-right">
                <div class="card-title">Match Played</div>
                <p class="card-subtitle text-50">
                    {{ $dataThisMonth }}
                    @if($dataThisMonth > 0)
                        <i class="material-icons text-success icon-16pt">keyboard_arrow_up</i>
                    @elseif($dataThisMonth < 0)
                        <i class="material-icons text-danger icon-16pt">keyboard_arrow_up</i>
                    @endif
                    From Last Month
                </p>
            </div>
        </div>
    </div>
</div>
