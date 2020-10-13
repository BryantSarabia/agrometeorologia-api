<div id="confirmPasswordModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="confirmPassword" method="POST" class="form-horizontal">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm your password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Please confirm your password to continue</p>
                    <input type="password" class="form-control" name="password">
                    <p class="text-danger"><small>Do not share your API KEY!</small></p>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-danger" data-dismiss="modal" value="Cancel">
                    <input type="submit" class="btn btn-primary" value="Confirm">
                </div>
            </form>
        </div>
    </div>
</div>
