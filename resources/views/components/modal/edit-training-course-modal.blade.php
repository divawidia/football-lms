<!-- Modal add lesson -->
<div class="modal fade" id="editTrainingVideoModal" tabindex="-1" aria-labelledby="editTrainingVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formEditTrainingVideoModal" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="training-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="trainingId">
                    <div class="form-group">
                        <label class="form-label" for="trainingTitle">Training Title</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="trainingTitle"
                               name="trainingTitle"
                               class="form-control"
                               placeholder="Input training's title ..."
                               required>
                        <span class="invalid-feedback trainingTitle_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="previewPhoto">Training Preview Image</label>
                        <small class="text-sm">(Optional)</small>
                        <div class="media justify-content-center mb-2">
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input"
                                       name="previewPhoto"
                                       id="previewPhoto"
                                       accept="image/jpg, image/jpeg, image/png">
                                <label class="custom-file-label" for="previewPhoto">Choose image</label>
                                <span class="invalid-feedback previewPhoto_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                            <img id="preview" class="image-upload-preview img-fluid mt-4" alt="image-preview" src=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="level">Difficulty Level</label>
                        <small class="text-danger">*</small>
                        <select class="form-control form-select" id="level" name="level" required>
                            <option disabled selected>Select training video's difficulty</option>
                            @foreach(['Beginner', 'Intermediate', 'Expert'] AS $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback level_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <small class="text-sm">(Optional)</small>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <span class="invalid-feedback description_error" role="alert">
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
<x-modal-form-update-processing formId="#formEditTrainingVideoModal"
                                updateDataId=""
                                :routeUpdate="$routeUpdate"
                                modalId="#editTrainingVideoModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            const formId = "#formEditTrainingVideoModal"
            $('#editTrainingVideo').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ $routeEdit }}",
                    type: 'GET',
                    success: function (res) {
                        $('#editTrainingVideoModal').modal('show');
                        $(formId+' #training-title').text('Edit Training Course: ' + res.data.trainingTitle);
                        $(formId+' #trainingId').val(res.data.id);
                        $(formId+' #trainingTitle').val(res.data.trainingTitle);
                        $(formId+' #level').val(res.data.level);
                        $(formId+' #description').text(res.data.description);
                        $(formId+' #preview').attr('src', "/storage/" + res.data.previewPhoto).addClass('d-block');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: "error",
                            title: "Something went wrong when deleting data!",
                            text: errorThrown,
                        });
                    }
                });
            });
        });
    </script>
@endpush
