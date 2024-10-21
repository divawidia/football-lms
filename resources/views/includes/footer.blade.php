<div class="bg-white border-top mt-auto">
    <div class="container page__container page-section d-flex flex-column">
        <p class="text-70 brand mb-24pt">
            @if(academyData()->logo)
                <img src="{{ Storage::url(academyData()->logo) }}" alt="" height="75">
                <h3 class="mb-0">
                    {{ academyData()->academyName }}
                </h3>
            @else
                LOGO
            @endif
        </p>
        <p class="measure-lead-max text-50 small mr-8pt">Lorem ipsum dolor sit amet consectetur. Pellentesque libero purus ac diam eget eu in vulputate. A bibendum dictum amet mauris nunc erat fusce sapien. Convallis non nisl ut sed neque fermentum elementum. Tempor venenatis bibendum justo phasellus.</p>
        <p class="mb-8pt d-flex">
            <a href=""
               class="text-70 text-underline mr-8pt small">Terms</a>
            <a href=""
               class="text-70 text-underline small">Privacy policy</a>
        </p>
        <p class="text-50 small mt-n1 mb-0">Copyright <script>document.write(new Date().getFullYear())</script>; All rights reserved.</p>
    </div>
</div>
