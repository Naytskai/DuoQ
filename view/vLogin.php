<div class="row">
    <div class="col-md-6">
        <div class="page-header">
            <h1>Login</h1>
        </div>
        <form class="form-horizontal" method="Post" role="form">
            <div class="form-group">
                <label for="mail" class="col-sm-4 control-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="mail" id="mail" placeholder="Email" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-4 control-label">Password</label>
                <div class="col-sm-6">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required="required">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-6">
                    <button type="submit" name="submitLogin" id="submitLogin" class="btn btn-default">Login</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-6 headerDivider">
        <div class="page-header">
            <h1>Register</h1>
        </div>
        <form class="form-horizontal" method="Post" role="form">
            <div class="form-group">
                <label for="newUsername" class="col-sm-4 control-label">Username</label>
                <div class="col-sm-6">
                    <input type="text" name="newUserName" class="form-control" id="newUsername" placeholder="Username" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="newMail" class="col-sm-4 control-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="newMail" id="newMail" placeholder="Email" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="newPassword" class="col-sm-4 control-label">Password</label>
                <div class="col-sm-6">
                    <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="Password" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="newPasswordConf" class="col-sm-4 control-label">Confirmation</label>
                <div class="col-sm-6">
                    <input type="password" class="form-control" name="newPasswordConf" id="newPasswordConf" placeholder="Password" required="required">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-6">
                    <button type="submit" name="submitRegister" id="submitRegister" class="btn btn-default">Register</button>
                </div>
            </div>
        </form>
    </div>
</div>