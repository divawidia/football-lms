<div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action='' method="post" id="formAddPlayer">
                @method('put')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Players</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label" for="players">Players</label>
                                <small class="text-danger">*</small>
                                <p>Select the desired players</p>
                                @if(count($players) == 0)
                                    <x-warning-alert text="Currently you haven't create any player in your academy or there is no players left, please create your new player" :createRoute="route('player-managements.create')"/>
                                @else
                                    <select class="form-control form-select" id="players" name="players[]" data-toggle="select" multiple>
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
                                    </select>
                                @endif
                                <span class="invalid-feedback players_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-modal-form-update-processing formId="#formAddPlayer"
                                updateDataId=""
                                :routeUpdate="$route"
                                modalId="#addPlayerModal"/>
@push('addon-script')
    <script type="module">
        $(document).ready(function () {
            $('#add-players').on('click', function (e) {
                e.preventDefault();
                $('#addPlayerModal').modal('show');
            });
        });
    </script>
@endpush
