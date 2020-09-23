<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('base/navbar/index');

if ($this->session) :

?>

    <style>
        body {
            background-image: url("<?= base_url(); ?>assets/backgrounds/login-bg.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }

        body:after {
            position: absolute;
            content: '';
            background-image: linear-gradient(to right, #434343 0%, black 100%);
            background-blend-mode: multiply, multiply;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: -1;
            opacity: 0.85;
        }

        #login {
            height: 100vh;
        }

        #login .card {
            height: auto;
            padding: 32px 16px;
        }

        #login .card #icon {
            padding: 0 16px;
            cursor: pointer;
        }

        .login-container {
            width: 20%;
        }

        .mobile-space {
            display: none;
        }

        @media(max-width: 1200px) {
            .login-container {
                width: 35%;
            }
        }

        @media(max-width: 992px) {
            .login-container {
                width: 45%;
            }
        }

        @media(max-width: 575px) {
            .login-container {
                width: 85%;
            }


            .mobile-space {
                display: block;
                height: 16px;
                width: 100%;
            }
        }
    </style>
    <div id="login" class="d-flex justify-content-center align-items-center">
        <div class="login-container">
            <div class="card">
                <form class="form">
                    <div class="form-group">
                        <label for="email2">Email address</label>
                        <input type="email" class="form-control" id="email2" placeholder="Enter Email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="position-relative d-flex align-items-center justify-content-center border rounded">
                            <input type="password" class="form-control border-0" id="password" placeholder="Password">
                            <i id="icon" class="fas fa-eye" onClick="showPassword()"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info d-block w-100 border-0 rounded">Log In</button>
                        <p class="d-block w-100 pt-2 pt-2 text-uppercase text-center">or</p>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success w-100 border-0 rounded">Sign up using <i class="fab fa-google pl-2"></i> </button>
                            </div>
                            <div class="mobile-space"></div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary w-100 border-0 rounded">Sign up using <i class="fab fa-facebook-f pl-2"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>

<?php endif; ?>

<?php $this->load->view('base/scripts/index'); ?>

<script>
    function showPassword() {
        var x = document.getElementById("password");
        var y = document.getElementById("icon");
        if (x.type === "password") {
            x.type = "text";
            y.className = "fas fa-eye-slash";
        } else {
            x.type = "password";
            y.className = "fas fa-eye";
        }
    }
</script> <?php $this->load->view('base/footer/index'); ?>