<div id="deleteProjectModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteProject" method="POST" class="form-horizontal">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Project</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>The project will be removed permanently. Are you sure?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <input id="delButton" type="button" class="btn btn-primary" data-dismiss="modal" value="Cancel">
                    <input type="submit" class="btn btn-danger" value="Delete">
                </div>
            </form>
        </div>
    </div>
</div>
