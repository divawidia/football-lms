<!-- Modal add lesson -->
<div class="modal fade" id="editTrainingVideoModal" tabindex="-1" aria-labelledby="editTrainingVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formEditTrainingVideoModal">
{{--                @method('PUT')--}}
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="add_trainingTitle">Training Title</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="add_trainingTitle"
                               name="trainingTitle"
                               class="form-control"
                               placeholder="Input training's title ..."
                               required>
                        <span class="invalid-feedback trainingTitle_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="add_previewPhoto">Training Preview Image</label>
                        <small class="text-danger">*</small>
                        <div class="media justify-content-center mb-2">
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input"
                                       name="previewPhoto"
                                       id="add_previewPhoto"
                                       accept="image/jpg, image/jpeg, image/png"
                                       required>
                                <label class="custom-file-label" for="add_previewPhoto">Choose image</label>
                                <span class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                            <img id="preview" class="image-upload-preview img-fluid mt-3" alt="image-preview" src=""/>
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
                        <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <small class="text-sm">(Optional)</small>
                        <div class="editor-container editor-container_classic-editor editor-container_include-style" id="editor-container">
                            <div class="editor-container__editor">
                                <textarea class="form-control editorTrainingVideo"
                                          id="description"
                                          name="description">
                                </textarea>
                            </div>
                        </div>
                        <span class="invalid-feedback description_error" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <input type="hidden" id="totalDuration" name="totalDuration">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
