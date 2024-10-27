<div id='{{ $calendarId }}'></div>

@push('addon-script')
    <script>
        $(document).ready(function () {
            const calendarEl = document.getElementById('{{ $calendarId }}');

            function getInitialView() {
                if (window.innerWidth >= 768 && window.innerWidth < 1200) {
                    return 'timeGridWeek';
                } else if (window.innerWidth <= 768) {
                    return 'listMonth';
                } else {
                    return 'dayGridMonth';
                }
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'local',
                editable: false,
                droppable: false,
                selectable: true,
                initialView: getInitialView(),
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                // responsive
                windowResize: function (view) {
                    const newView = getInitialView();
                    calendar.changeView(newView);
                },
                events: @json($events)
            });
            calendar.render();
        });
    </script>
@endpush

