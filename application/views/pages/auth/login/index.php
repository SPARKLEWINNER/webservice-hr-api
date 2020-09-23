<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('base/header/index');
// session_destroy();
if (!empty($this->session->id)) : header('/client');
else :
?>

    <div id="login" class="d-flex justify-content-center align-items-center">
        <div class="login-container">
            <div class="card">
                <form class="form" id="loginForm" onSubmit="request(event)" autocomplete="on">
                    <div class="form-group">
                        <label for="emailAddress">Email address</label>
                        <input type="email" class="form-control" name="email" id="emailAcc" placeholder="Enter Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="position-relative d-flex align-items-center justify-content-center border rounded">
                            <input type="password" class="form-control border-0" name="password" id="passwordAcc" placeholder="Password" autocomplete="on" required>
                            <i id="icon" class="fas fa-eye" onClick="showPassword()"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info d-block w-100 border-0 rounded">Log In</button>
                        <p class="d-block w-100 pt-2 pt-2 text-uppercase text-center">or</p>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success w-100 border-0 rounded">Sign in using <i class="fab fa-google pl-2"></i> </button>
                            </div>
                            <div class="mobile-space"></div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary w-100 border-0 rounded">Sign in using <i class="fab fa-facebook-f pl-2"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>

<?php
endif;
?>

<?php $this->load->view('base/scripts/index'); ?>

<script>
    function showPassword() {
        var x = document.getElementById("passwordAcc");
        var y = document.getElementById("icon");
        if (x.type === "password") {
            x.type = "text";
            y.className = "fas fa-eye-slash";
        } else {
            x.type = "password";
            y.className = "fas fa-eye";
        }
    }

    function request(e) {
        let base_url = "<?php echo base_url(); ?>";
        e.preventDefault();
        $.post({
            url: base_url + "client/authentication/login",
            data: $("#loginForm").serialize(),
            dataType: "json",
            success: function(result) {
                console.log(result.request);
                if (result.request) {
                    location.href = base_url + "home";
                } else {
                    failed(result.message);
                }
            },

        });
    }
</script> <?php $this->load->view('base/footer/index'); ?>