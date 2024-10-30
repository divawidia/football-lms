<div class="card">
    <div class="card-header d-flex align-items-center">
        <div class="flex">
            <h4 class="card-title">{{ date('D, M d Y h:i A', strtotime($review->created_at)) }}</h4>
            <div class="card-subtitle text-50">Last updated at {{ date('D, M d Y h:i A', strtotime($review->updated_at)) }}</div>
        </div>
    </div>
    <div class="card-body">
        @php
            echo $review->performanceReview
        @endphp
    </div>
</div>
