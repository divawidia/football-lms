<div class="modal fade" id="editTeamStandingModal" tabindex="-1" aria-labelledby="editTeamStandingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formEditTeamStanding">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Team League Standing</h5>
                    <x-buttons.basic-button :modalCloseIcon="true" :modalDismiss="true"/>
                </div>
                <div class="modal-body">
                    <x-forms.basic-input type="hidden" name="standingId" :modal="true"/>

                    <div class="row">
                        <div class="col-4">
                            <x-forms.basic-input type="number" min="0" name="matchPlayed" label="Match Played" :modal="true"/>
                            <x-forms.basic-input type="number" min="0" name="won" label="Match Won" :modal="true"/>
                            <x-forms.basic-input type="number" min="0" name="drawn" label="Match Drawn" :modal="true"/>
                        </div>
                        <div class="col-4">
                            <x-forms.basic-input type="number" min="0" name="lost" label="Match lost" :modal="true"/>
                            <x-forms.basic-input type="number" min="0" name="goalsFor" label="Goals For" :modal="true"/>
                            <x-forms.basic-input type="number" min="0" name="goalsAgainst" label="Goals Against" :modal="true"/>
                        </div>
                        <div class="col-4">
                            <x-forms.basic-input type="number" min="0" name="goalsDifference" label="Goals Difference" :modal="true"/>
                            <x-forms.basic-input type="number" min="0" name="points" label="Points" :modal="true"/>
                            <x-forms.basic-input type="number" min="0" name="standingPositions" label="Standing Positions" :modal="true"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <x-buttons.basic-button type="button" color="secondary" :modalDismiss="true" text="Cancel"/>
                    <x-buttons.basic-button type="submit" text="Submit"/>
                </div>
            </form>
        </div>
    </div>
</div>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;

        $(document).ready(function (){
            const formId = '#formEditTeamStanding';
            const modalId = '#editTeamStandingModal';

            $('body').on('click', '.edit-team-standing-btn', function(e) {
                e.preventDefault();
                const standingId = $(this).attr('id');
                $(modalId).modal('show');

                $.ajax({
                    url: "{{ route('competition-managements.league-standings.show', ['competition' => $competition->hash, 'leagueStanding'=>':id']) }}".replace(':id', standingId),
                    type: 'GET',
                    success: function(res) {
                        $(formId+' #standingId').val(res.data.id)
                        $.each(res.data, function (key, value) {
                            $(formId+' #'+key).val(value)
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
            });

            processModalForm(
                formId,
                "{{ route('competition-managements.league-standings.update', ['competition' => $competition->hash, 'leagueStanding'=>':id']) }}",
                "#standingId",
                modalId
            );
        });
    </script>
@endpush
