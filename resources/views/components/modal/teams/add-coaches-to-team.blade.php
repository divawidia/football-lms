<x-modal.form id="addCoachModal" formId="formAddCoach" title="Add Team's Coaches" :editForm="true" size="">
    @if(count($coaches) == 0)
        <x-warning-alert text="Currently there is no coaches available or you haven't create any coach in your academy, please create your new coach" :createRoute="route('coach-managements.create')"/>
    @else
        <x-forms.select name="coaches[]" id="coaches" :multiple="true" label="Coaches" :modal="true">
            <option disabled>Select coaches</option>
            @foreach($coaches as $coach)
                <option value="{{ $coach->id }}" data-avatar-src="{{ Storage::url($coach->user->foto) }}">
                    {{ $coach->user->firstName }} {{ $coach->user->lastName }} - {{ $coach->specialization->name }} -
                    @if(count($coach->teams) == 0)
                        No Team
                    @else
                        @foreach($coach->teams as $team)
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
            showModal('#add-coaches', '#addCoachModal', '#formAddCoach')

            // ajax store form
            processModalForm(
                '#formAddCoach',
                "{{ $route }}",
                null,
                '#addCoachModal'
            );
        });
    </script>
@endpush
