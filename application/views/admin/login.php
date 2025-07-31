<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/usertemplate/assets/bootstrap/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/images/s-favican.png" rel="shortcut icon" type="image/x-icon">

    <title>Multi Inventory System</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: row;
            height: 100vh;
            font-family: 'Lato', sans-serif;
            /* Smooth and professional look */
        }

        .left-panel {
            flex: 2;
            background: url('<?= base_url('uploads/Capture.JPG'); ?>') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .right-panel {
            flex: 1;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-container img {
            max-width: 150px;
            /* Increased size for better visibility */
            width: 100%;
            height: auto;
            /* Maintain aspect ratio */
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
            /* Optional: Add shadow effect */
            border-radius: 10%;
        }

        .tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 10px;
        }

        .tabs button {
            flex: 1;
            padding: 10px;
            font-size: 1rem;
            border: none;
            background: #f8f9fa;
            color: #333;
            cursor: pointer;
        }

        .tabs button.active {
            background: #28a745;
            color: #fff;
        }

        .form-container h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .form-group .icon-container {
            background: #28a745;
            padding: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-group .icon-container i {
            color: #fff;
            font-size: 1.2rem;
        }

        .form-group input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
        }

        .form-group input:focus {
            border: none;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #218838;
        }

        .justify {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Optional: Aligns items vertically */
        }

        .text-red {
            color: red;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .left-panel {
                flex: none;
                width: 100%;
                height: 50vh;
                text-align: center;
            }

            .right-panel {
                flex: none;
                width: 100%;
                height: auto;
                padding: 15px;
            }

            .form-container {
                padding: 10px;
            }

            .tabs {
                flex-direction: column;
            }

            .tabs button {
                margin-bottom: 5px;
            }
        }

        @media (max-width: 480px) {
            .form-container img {
                max-width: 70px;
            }

            .form-group .icon-container i {
                font-size: 1rem;
            }

            .login-btn {
                font-size: 0.9rem;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Roboto:wght@300;400;500&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

</head>

<body>
    <div class="left-panel">
        <!-- <img src="/uploads/logo.JPG" alt="Logo">
    <h1>Medicine Inventory Management System</h1>
    <p>Version: 1.0</p> -->
    </div>
    <div class="right-panel">
        <div class="form-container">
            <img src="<?= base_url('uploads/logo.JPG'); ?>" alt="Logo">
            <div class="tabs">
                <button class="active">Login here</button>
                <button>Sign up / Register here</button>
            </div>
            <h2>Login</h2>
            <!-- <p>Choose Your Login Type!</p> -->

            <?php
                                    if (isset($error_message)) {
                                        echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                    }
                                    ?>
                                    <?php
                                    if ($this->session->flashdata('message')) {
                                        echo "<div class='alert alert-success'>" . $this->session->flashdata('message') . "</div>";
                                    };
                                    ?>
                                    <form action="<?php echo site_url('site/login') ?>" method="post">
            <?php echo $this->customlib->getCSRF(); ?>

                <div class="justify ">
                    <h3>Username</h3>

                </div>
                <div class="form-group">

                    <div class="icon-container">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <input type="text" name="username" placeholder="<?php echo $this->lang->line('username'); ?>" value="" class="form-username " id="email">
                    <span class="text-danger"><?php echo form_error('username'); ?></span>
                </div>
                <div class="justify ">
                    <h3>Password</h3>
                </div>
                <div class="form-group">
                    <div class="icon-container">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" value="" name="password" placeholder="<?php echo $this->lang->line('password'); ?>" class="form-password " id="password">
                    <span class="text-danger"><?php echo form_error('password'); ?></span>
                </div>


                <button type="submit" class="login-btn"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>
        </div>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>

<script>
      $(document).ready(function () {
        var base_url = '<?php echo base_url(); ?>';
        $.backstretch([
            base_url + "backend/usertemplate/assets/img/backgrounds/11.jpg"
        ], {duration: 3000, fade: 750});
        $('.login-form input[type="text"], .login-form input[type="password"], .login-form textarea').on('focus', function () {
            $(this).removeClass('input-error');
        });
        $('.login-form').on('submit', function (e) {
            $(this).find('input[type="text"], input[type="password"], textarea').each(function () {
                if ($(this).val() == "") {
                    e.preventDefault();
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });
        });
    });
</script>
