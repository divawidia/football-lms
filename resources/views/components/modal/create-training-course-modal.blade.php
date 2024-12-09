<!-- Modal add lesson -->
<div class="modal fade" id="addTrainingVideoModal" tabindex="-1" aria-labelledby="addTrainingVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formAddTrainingVideoModal" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="training-title text-capitalize">Create new training Course</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="trainingTitle">Training Title</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="trainingTitle"
                               name="trainingTitle"
                               class="form-control"
                               placeholder="Input training's title ..."
                               required>
                        <span class="invalid-feedback trainingTitle" role="alert">
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
                                <span class="invalid-feedback previewPhoto" role="alert">
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
                            <option disabled selected>Select training course's difficulty</option>
                            @foreach(['Beginner', 'Intermediate', 'Expert'] AS $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback level" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <small class="text-sm">(Optional)</small>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <span class="invalid-feedback description" role="alert">
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

<x-modal-form-update-processing formId="#formAddTrainingVideoModal"
                                updateDataId=""
                                :routeUpdate="route('training-videos.store')"
                                modalId="#addTrainingVideoModal"/>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('#addTrainingVideo').on('click', function (e) {
                e.preventDefault();
                $('#addTrainingVideoModal').modal('show');
            });
        });
    </script>
@endpush
