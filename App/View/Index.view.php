<div class="main">
    <!-- Sign up form -->
    <div class="section-signup-form display-off">
        <div class="container">
            <div class="signup-content">
                <div class="signup-form">
                    <h2 class="form-title">Sign up</h2>
                    <form method="POST" class="register-form" id="register-form">
                        <div class="form-group">
                            <label for="first_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                            <input type="text" name="first_name" id="first_name" placeholder="First Name" required/>
                        </div>
                        <div class="form-group">
                            <label for="last_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                            <input type="text" name="last_name" id="last_name" placeholder="Last Name" required/>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="zmdi zmdi-email"></i></label>
                            <input type="email" name="email" id="email" placeholder="Your Email" required/>
                        </div>
                        <div class="form-group">
                            <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                            <input type="password" name="pass" id="pass" placeholder="Password" required/>
                        </div>
                        <div class="form-group">
                            <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                            <input type="password" name="re_pass" id="re_pass" placeholder="Repeat your password" required/>
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="signup" id="signup" class="form-submit" value="Register" onclick="insertUser()"/>
                        </div>
                    </form>
                </div>
                <div class="signup-image">
                    <figure><img src="<?php echo $data['signup-image'] ?>" alt="sing up image"></figure>
                    <a href="#" class="signin-image-link">I am already member</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sing in  Form -->
    <div class="section-signin-form display-on">
        <div class="container">
            <div class="signin-content">
                <div class="signin-image">
                    <figure><img src=<?php echo $data['signin-image'] ?> alt="sing up image"></figure>
                    <a href="#" class="signup-image-link">Create an account</a>
                </div>

                <div class="signin-form">
                    <h2 class="form-title">Sign up</h2>
                    <form method="POST" class="register-form" id="login-form">
                        <div class="form-group">
                            <label for="your_email"><i class="zmdi zmdi-account material-icons-name"></i></label>
                            <input type="text" name="email" id="your_email" placeholder="Your Email" />
                        </div>
                        <div class="form-group">
                            <label for="your_pass"><i class="zmdi zmdi-lock"></i></label>
                            <input type="password" name="pass" id="your_pass" placeholder="Password" />
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="remember-me" id="remember-me" class="agree-term" />
                            <label for="remember-me" class="label-agree-term"><span><span></span></span>Remember me</label>
                        </div>
                        <div class="form-group form-button">
                            <input type="submit" name="signin" id="signin" class="form-submit" value="Log in" onclick="loginUser()" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>