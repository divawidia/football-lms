<x-modal.form id="addPlayerModal" formId="formAddCoach" title="Add Team's players" :editForm="true" size="">
    @if(count($players) == 0)
        <x-warning-alert text="Currently there is no players available or you haven't create any player in your academy, please create your new player" :createRoute="route('player-managements.create')"/>
    @else
        <x-forms.select name="players[]" id="players" :multiple="true" label="players" :modal="true">
            <option disabled>Select players</option>
            @foreach($players as $player)
                <option value="{{ $player->id }}" data-avatar-src="{{ Storage::url($player->user->foto) }}">
                    {{ $player->user->firstName }} {{ $player->user->lastName }} - {{ $player->position->name }} -
                    @if(count($player->teams) == 0)
                        No Team
                    @else
                        @foreach($player->teams as $team)
                            {{ $team->teamName }},
                        @endforeach
                    @endif
                </option>
            @endforeach
        </x-forms.select>
    @endif
</x-modal.form>

@push('addon-script')
    <script>
        $(document).ready(function () {
            showModal('#add-players', '#addPlayerModal', '#formAddCoach')

            // ajax store form
            processModalForm(
                '#formAddCoach',
                "{{ $route }}",
                null,
                '#addPlayerModal'
            );
        });
    </script>
@endpush
