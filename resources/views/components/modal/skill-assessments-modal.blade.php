<!-- Modal add lesson -->
<div class="modal fade" id="addSkillStatsModal" tabindex="-1" aria-labelledby="addSkillStatsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" id="formAddSkillStatsModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="training-title">Add Player Skill Stats</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="eventId" name="eventId"/>
                    <input type="hidden" id="playerId"/>
                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="controlling">Controlling : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="controlling" name="controlling" class="skills-range-slider" required/>
                                <span class="invalid-feedback controlling_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="recieving">Receiving : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="recieving" name="recieving" class="skills-range-slider" required/>
                                <span class="invalid-feedback recieving_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="dribbling">Dribbling : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="dribbling" name="dribbling" class="skills-range-slider" required/>
                                <span class="invalid-feedback dribbling_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="passing">Passing : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="passing" name="passing" class="skills-range-slider" required/>
                                <span class="invalid-feedback passing_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="shooting">Shooting : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="shooting" name="shooting" class="skills-range-slider" required/>
                                <span class="invalid-feedback shooting_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="crossing">Crossing : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="crossing" name="crossing" class="skills-range-slider" required/>
                                <span class="invalid-feedback crossing_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="turning">Turning : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="turning" name="turning" class="skills-range-slider" required/>
                                <span class="invalid-feedback turning_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="ballHandling">Ball Handling : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="ballHandling" name="ballHandling" class="skills-range-slider" required/>
                                <span class="invalid-feedback ballHandling_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="powerKicking">Power Kicking : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="powerKicking" name="powerKicking" class="skills-range-slider" required/>
                                <span class="invalid-feedback powerKicking_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="goalKeeping">Goal Keeping : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="goalKeeping" name="goalKeeping" class="skills-range-slider" required/>
                                <span class="invalid-feedback goalKeeping_error" role="alert">
                                <strong></strong>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="offensivePlay">Offensive Play : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="offensivePlay" name="offensivePlay" class="skills-range-slider" required/>
                                <span class="invalid-feedback offensivePlay_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="defensivePlay">Defensive Play : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="offensivePlay" name="defensivePlay" class="skills-range-slider" required/>
                                <span class="invalid-feedback defensivePlay_error" role="alert">
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

<x-modal-form-update-processing formId="#formAddSkillStatsModal"
                                updateDataId="#formAddSkillStatsModal #playerId"
                                :routeUpdate="route('skill-assessments.store', ['player' => ':id'])"
                                modalId="#addSkillStatsModal"/>

@push('addon-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>
    <script>
        $(document).ready(function (){
            $(".skills-range-slider").ionRangeSlider({
                grid: true,
                values: [
                    "Poor", "Needs Work", "Average Fair", "Good", "Excellent"
                ]
            });

            // show add skill stats form modal when update skill button clicked
            $('body').on('click', '.addSkills', function (e) {
                e.preventDefault();
                const playerId = $(this).attr('id');
                const eventId = $(this).attr('data-eventId');
                const formId = '#formAddSkillStatsModal';

                $('#addSkillStatsModal').modal('show');
                $(formId+' #eventId').val(eventId);
                $(formId+' #playerId').val(playerId);
            });
        });
    </script>
@endpush
