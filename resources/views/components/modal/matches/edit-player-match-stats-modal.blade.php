<x-modal.form id="playerMatchStatsModal" formId="formPlayerMatchStats" title="Edit Player Match Stats" :editForm="true" size="">
    <div class="row">
        <div class="col-6">
            <x-forms.basic-input type="hidden" name="playerStatsId"/>
            <x-forms.basic-input type="number" name="minutesPlayed" label="Minutes Played" placeholder="Input minutes played" min="0" :modal="true"/>
            <x-forms.basic-input type="number" name="shots" label="Shots" placeholder="Input team shots" min="0" :modal="true"/>
            <x-forms.basic-input type="number" name="passes" label="Passes" placeholder="Input team passes" min="0" :modal="true"/>
            <x-forms.basic-input type="number" name="fouls" label="Fouls" placeholder="Input team fouls" min="0" :modal="true"/>
        </div>
        <div class="col-6">
            <x-forms.basic-input type="number" name="yellowCards" label="Yellow Cards" placeholder="Input yellow cards" min="0" :modal="true"/>
            <x-forms.basic-input type="number" name="redCards" label="Red Cards" placeholder="Input red cards" min="0" :modal="true"/>
            <x-forms.basic-input type="number" name="saves" label="Saves" placeholder="Input saves" min="0" :modal="true"/>
        </div>
    </div>
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = '#formPlayerMatchStats';
            const modalId = '#playerMatchStatsModal';

            // show create team scorer modal
            $('body').on('click', '.edit-player-stats', function (e) {
                e.preventDefault();
                const id = $(this).attr('id');
                $(modalId).modal('show');
                clearModalFormValidation(formId)

                $.ajax({
                    url: "{{ route('match-schedules.player-match-stats.show', ['match' => $match->id, 'player' => ":id"]) }}".replace(':id', id),
                    type: 'GET',
                    success: function (res) {
                        $(formId+' .modal-title').text('Update Player ' + res.data.playerData.firstName + ' ' + res.data.playerData.lastName + ' Stats');
                        $(formId+' #playerStatsId').val(res.data.statsData.playerId);
                        $.each(res.data.statsData, function (key, val) {
                            $('#' + key).val(val);
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            icon: "error",
                            title: errorThrown,
                            text: response.message,
                        });
                    }
                });
            });

            processModalForm(formId, "{{ route('match-schedules.player-match-stats.update', ['match' => $match->hash, 'player' => ':id']) }}", "#playerStatsId", modalId);
        });
    </script>
@endpush
