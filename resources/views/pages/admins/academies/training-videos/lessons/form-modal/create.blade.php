<!-- Modal add lesson -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-labelledby="addLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post" id="formAddLessonModal">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add lesson to Training</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="lessonTitle">Lesson Title</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="lessonTitle"
                               name="lessonTitle"
                               class="form-control"
                               placeholder="Input lesson's title ..."
                               required>
                        <span class="invalid-feedback lessonTitle" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <small class="text-sm">(Optional)</small>
                        <div class="editor-container editor-container_classic-editor editor-container_include-style" id="editor-container">
                            <div class="editor-container__editor">
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                        </div>
                        <span class="invalid-feedback description" role="alert">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="lessonVideoURL">Lesson Video URL</label>
                        <small class="text-danger">*</small>
                        <input type="text"
                               id="lessonVideoURL"
                               name="lessonVideoURL"
                               class="form-control"
                               placeholder="Input youtube video url (only from youtube!) ..."
                               required>
                        <span class="invalid-feedback lessonVideoURL" role="alert">
                            <strong></strong>
                        </span>
                        <div id="preview-container">
                            <div id="player"></div>
                        </div>
                    </div>
                    <input type="hidden" id="totalDuration" name="totalDuration">
                    <span class="invalid-feedback totalDuration" role="alert">
                        <strong></strong>
                    </span>
                    <input type="hidden" id="videoId" name="videoId">
                    <span class="invalid-feedback videoId" role="alert">
                        <strong></strong>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
