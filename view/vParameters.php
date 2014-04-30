<div class="page-header">
    <h1>Parameters</h1>
</div>
<form class="form-horizontal" method="Post" role="form">
    <div class="form-group">
        <label for="newUsername" class="col-sm-4 control-label">Username</label>
        <div class="col-sm-4">
            <input type="text" name="newUserName" value="<?php echo $username ?>" class="form-control" id="newUsername" placeholder="Username" required="required">
        </div>
    </div>
    <div class="form-group">
        <label for="newMail" class="col-sm-4 control-label">Email</label>
        <div class="col-sm-4">
            <input type="email" class="form-control" value="<?php echo $userMail ?>" name="newMail" id="newMail" placeholder="Email" required="required">
        </div>
    </div>
    <div class="form-group">
        <label for="newPassword" class="col-sm-4 control-label">Password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="Password" autocomplete="off" required="required">
        </div>
    </div>
    <div class="form-group">
        <label for="newPasswordConf" class="col-sm-4 control-label">Password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" name="newPasswordConf" id="newPasswordConf" placeholder="Password Confirmation" autocomplete="off" required="required">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-6">
            <button type="submit" name="submitUpdate" id="submitRegister" class="btn btn-default">Save</button>
        </div>
    </div>
</form>