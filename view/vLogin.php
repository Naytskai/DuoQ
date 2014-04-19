<div class="row">
    <div class="col-md-6">
        <form class="form-horizontal" method="Post" role="form">
            <div class="form-group">
                <div class="col-sm-offset-1">
                    <h2>Login</h2>
                </div>
            </div>
            <div class="form-group">
                <label for="mail" class="col-sm-4 control-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="mai" id="mail" placeholder="Email">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-4 control-label">Password</label>
                <div class="col-sm-6">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
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
        <form class="form-horizontal" method="Post" role="form">
            <div class="form-group">
                <div class="col-sm-offset-1">
                    <h2>Register</h2>
                </div>
            </div>
            <div class="form-group">
                <label for="newUsername" class="col-sm-4 control-label">Username</label>
                <div class="col-sm-6">
                    <input type="text" name="newUserName" class="form-control" id="newUsername" placeholder="Username">
                </div>
            </div>
            <div class="form-group">
                <label for="newMail" class="col-sm-4 control-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="newMail" id="newMail" placeholder="Email">
                </div>
            </div>
            <div class="form-group">
                <label for="newPassword" class="col-sm-4 control-label">Password</label>
                <div class="col-sm-6">
                    <input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <label for="newPasswordConf" class="col-sm-4 control-label">Confirmation</label>
                <div class="col-sm-6">
                    <input type="password" class="form-control" name="newPasswordConf" id="newPasswordConf" placeholder="Password">
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