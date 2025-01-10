<x-modal.form id="createNewTeamModal" formId="formCreateNewTeam" title="Create New Team">
    <x-forms.image-input name="logo" label="Team Logo" :modal="true"/>

    <div class="row">
        <div class="col-lg-6">
            <x-forms.basic-input type="text" name="teamName" label="Team Name" placeholder="Input team's name ..." :modal="true"/>
        </div>
        <div class="col-lg-6">
            <x-forms.select name="ageGroup" label="Age Group" :modal="true">
                <option disabled selected>Select team's age group</option>
                @foreach(['U-6', 'U-7', 'U-8', 'U-9', 'U-10', 'U-11', 'U-12', 'U-13', 'U-14', 'U-15', 'U-16', 'U-17', 'U-18', 'U-19', 'U-20', 'U-21', 'Senior'] AS $ageGroup)
                    <option
                        value="{{ $ageGroup }}">{{ $ageGroup }}</option>
                @endforeach
            </x-forms.select>
        </div>
    </div>
</x-modal.form>

@push('addon-script')
    <script type="module">
        import { processModalForm } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;
        import { showModal } from "{{ Vite::asset('resources/js/modal.js') }}" ;

        $(document).ready(function () {
            showModal('#addNewTeam', '#createNewTeamModal', '#formCreateNewTeam')

            // ajax store form
            processModalForm(
                '#formCreateNewTeam',
                "{{ route('team-managements.apiStore') }}",
                null,
                '#createNewTeamModal'
            );
        });
    </script>
@endpush
