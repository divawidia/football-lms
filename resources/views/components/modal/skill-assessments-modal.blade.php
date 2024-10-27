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
                    <div class="form-group">
                        <div class="row d-flex flex-row align-items-center mb-2">
                            <div class="col-md-3">
                                <label class="form-label" for="controlling">Controlling : </label>
                                <small class="text-danger">*</small>
                            </div>
                            <div class="col-md-9">
                                <input type="text" id="controlling" name="controlling" class="skills-range-slider" required/>
                                <span class="invalid-feedback controlling" role="alert">
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
                                <span class="invalid-feedback recieving" role="alert">
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
                                <span class="invalid-feedback dribbling" role="alert">
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
                                <span class="invalid-feedback passing" role="alert">
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
                                <span class="invalid-feedback shooting" role="alert">
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
                                <span class="invalid-feedback crossing" role="alert">
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
                                <span class="invalid-feedback turning" role="alert">
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
                                <span class="invalid-feedback ballHandling" role="alert">
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
                                <span class="invalid-feedback powerKicking" role="alert">
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
                                <span class="invalid-feedback goalKeeping" role="alert">
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
                                <span class="invalid-feedback offensivePlay" role="alert">
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
                                <span class="invalid-feedback defensivePlay" role="alert">
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

@push('addon-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
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
            $('#addSkills').on('click', function (e) {
                e.preventDefault();
                $('#addSkillStatsModal').modal('show');
            });

            // store skill stats data when form submitted
            $('#formAddSkillStatsModal').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ $route }}",
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#addSkillStatsModal').modal('hide');
                        Swal.fire({
                            title: 'Player skill stats successfully added!',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: "#1ac2a1",
                            confirmButtonText:
                                'Ok!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        const response = JSON.parse(jqXHR.responseText);
                        $.each(response.errors, function (key, val) {
                            $('#formAddSkillStatsModal span.' + key).text(val[0]);
                            $("#formAddSkillStatsModal #" + key).addClass('is-invalid');
                        });
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when creating data!",
                            text: errorThrown,
                        });
                    }
                });
            });
        });
    </script>
@endpush
