<x-modal.form id="addTeamModal" formId="formAddTeam" :editForm="true" title="Add to Team">
    @if(count($teams) < 1)
        <x-warning-alert text="Currently there is no team available in your academy, please create your team" :createRoute="route('team-managements.create')"/>
    @else
        <x-forms.select name="teams" label="teams" :modal="true" :select2="true">
            <option disabled selected>Select teams</option>
            @foreach($teams as $team)
                <option value="{{ $team->id }}" data-avatar-src="{{ Storage::url($team->logo) }}">{{ $team->teamName }}</option>
            @endforeach
        </x-forms.select>
    @endif
</x-modal.form>

@push('addon-script')
    <script>

        $(document).ready(function () {
            $('.add-team').on('click', function (e) {
                e.preventDefault();
                $('#addTeamModal').modal('show');
            });

            processModalForm(
                "#formAddTeam",
                "{{ $route }}",
                "",
                "#addTeamModal"
            );
        });
    </script>
@endpush
