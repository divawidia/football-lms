<!-- Modal add lesson -->
<div class="modal fade" id="assignPlayerModal" tabindex="-1" aria-labelledby="assignPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formAssignPlayerModal">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign player to {{ $data->trainingTitle }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="players">Select Player</label>
                        <small class="text-danger">*</small>
                        @if(count($players) == 0)
                            <div class="alert alert-light border-1 border-left-4 border-left-accent"
                                 role="alert">
                                <div class="d-flex flex-wrap align-items-center">
                                    <i class="material-icons mr-8pt">error_outline</i>
                                    <div class="media-body"
                                         style="min-width: 180px">
                                        <small class="text-black-100">Curently you haven't create any player in your academy, please create your team</small>
                                    </div>
                                    <div class="ml-8pt mt-2 mt-sm-0">
                                        <a href="{{ route('player-managements.create') }}"
                                           class="btn btn-link btn-sm">Create Now</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <select class="form-control form-select @error('players') is-invalid @enderror" id="players" name="players[]" data-toggle="select" multiple>
                                <option disabled>Assign player to this training</option>
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
                        <span class="invalid-feedback playerId" role="alert">
                            <strong></strong>
                        </span>
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
