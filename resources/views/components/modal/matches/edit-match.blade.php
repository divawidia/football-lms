
<x-modal.form id="editMatchModal" formId="formEditMatch" title="Edit Team Match Session" :editForm="true">
    <x-forms.basic-input type="hidden" name="matchId" :modal="true"/>

    @if ($competition->isInternal == 1)
        <x-forms.select name="homeTeamId" label="Home Team" :modal="true" :select2="false"></x-forms.select>
        <x-forms.select name="awayTeamId" label="Away Team" :modal="true" :select2="false"></x-forms.select>
    @else
        <x-forms.select name="homeTeamId" label="Team" :modal="true" :select2="false"></x-forms.select>
        <x-forms.basic-input type="text" name="externalTeamName" label="Opossing Team" placeholder="Input the opposing team (external team) of this match ..." :modal="true"/>
    @endif

    <x-forms.basic-input type="date" name="date" label="Match Date" :modal="true"/>

    <div class="row">
        <div class="col-6">
            <x-forms.basic-input type="time" name="startTime" label="Match Start Time" placeholder="Input match start time ..." :modal="true"/>
        </div>
        <div class="col-6">
            <x-forms.basic-input type="time" name="endTime" label="Match End Time" placeholder="Input match end time ..." :modal="true"/>
        </div>
    </div>

    <x-forms.basic-input type="text" name="place" label="Match Venue" placeholder="Input match venue ..." :modal="true"/>
</x-modal.form>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}";
        import { clearModalFormValidation } from "{{ Vite::asset('resources/js/modal.js') }}";

        $(document).ready(function (){
            const formId = '#formEditMatch';
            const modalId = '#editMatchModal';

            function getHomeTeams(){
                $.ajax({
                    url: "{{ route('team-managements.all-teams') }}",
                    type: 'GET',
                    success: function(res) {
                        $(formId+' #homeTeamId').html('<option disabled>Select home team in this match</option>');
                        $.each(res.data, function (key, value) {
                            $(formId+' #homeTeamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when retrieving data!",
                            text: errorThrown,
                        });
                    }
                });
            }

            function getAwayTeams(exceptTeamId = null){
                $.ajax({
                    url: "{{route('team-managements.all-teams') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        exceptTeamId: exceptTeamId
                    },
                    success: function (result) {
                        $(formId+' #awayTeamId').html('<option disabled>Select away team in this match</option>');
                        $.each(result.data, function (key, value) {
                            $(formId+' #awayTeamId').append('<option value=' + value.id + '>' + value.teamName + '</option>');
                        });
                    }
                });
            }

            $('body').on('click', '.edit-match-btn', function(e) {
                e.preventDefault();
                const matchId = $(this).attr('id');
                $(modalId).modal('show');
                clearModalFormValidation(formId)
                getHomeTeams()

                $.ajax({
                    url: "{{ route('match-schedules.match-detail', ':id') }}".replace(':id', matchId),
                    type: 'GET',
                    success: function(res) {
                        $(formId+' #matchId').val(res.data.schedule.id)

                        $(formId+' #homeTeamId').val(res.data.schedule.homeTeamId)

                        @if($competition->isInternal == 1)
                            getAwayTeams(res.data.schedule.homeTeamId)
                            $(formId+' #awayTeamId').val(res.data.schedule.awayTeamId);
                        @else
                            $(formId+' #externalTeamName').val(res.data.opposingTeam);
                        @endif

                        $(formId+' #date').val(res.data.schedule.date);
                        $(formId+' #startTime').val(res.data.schedule.startTime.substring(0, 5));
                        $(formId+' #endTime').val(res.data.schedule.endTime.substring(0, 5));
                        $(formId+' #place').val(res.data.schedule.place);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when retrieving data!",
                            text: errorThrown,
                        });
                    }
                });
            });

            @if($competition->isInternal == 1)
                $(formId+' #homeTeamId').on('change', function () {
                    const id = this.value;
                    getAwayTeams(id)
                });
            @endif

            processModalForm(
                formId,
                "{{ route('match-schedules.update', ':id') }}",
                "#matchId",
                modalId
            );
        });
    </script>
@endpush
