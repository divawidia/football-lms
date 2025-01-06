<!-- Modal Create Opponent Team -->
<div class="modal fade" id="createNewOpponentTeamModal" tabindex="-1"
     aria-labelledby="createNewOpponentTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post"
                  enctype="multipart/form-data" id="formCreateNewOpponentTeam">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create new opponent team</h5>
                    <x-buttons.basic-button :modalCloseIcon="true" :modalDismiss="true"/>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <x-forms.image-input name="logo" label="Competition Logo" :modal="true"/>
                        </div>
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
        import { processModalForm, showModal } from "{{ Vite::asset('resources/js/ajax-processing-data.js') }}" ;

        $(document).ready(function () {
            showModal('#addNewOpponentTeam', '#createNewOpponentTeamModal')

            // ajax store from
            processModalForm(
                '#formCreateNewOpponentTeam',
                "{{ route('opponentTeam-managements.apiStore') }}",
                null,
                '#createNewOpponentTeamModal'
            );
        });
    </script>
@endpush
