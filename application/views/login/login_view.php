<body>
    <!-- [ Preloader ] Start -->
    <div class="page-loader">
        <div class="bg-primary"></div>
    </div>
    <!-- [ Preloader ] End -->

    <!-- [ content ] Start -->
    <div class="authentication-wrapper authentication-1 px-4">
        <div class="authentication-inner py-5">

            <!-- [ Logo ] Start -->
            <div class="d-flex justify-content-center align-items-center">
                <div class="ui-w-60">
                    <div class="w-100 position-relative">
                        <img src="<?=base_url()?>assets/img/logo-dark.png" alt="Brand Logo" class="img-fluid">
                    </div>
                </div>
            </div>
            <!-- [ Logo ] End -->

            <!-- [ Form ] Start -->
            <form class="my-5" id="form_login">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="us_email">
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="form-label d-flex justify-content-between align-items-end">
                        <span>Password</span>
                    </label>
                    <input type="password" class="form-control"name="us_password">
                    <div class="clearfix"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center m-0">
                    <button type="submit" class="btn btn-primary" onclick="login('form_login');">Sign In</button>
                </div>
            </form>
            <!-- [ Form ] End -->
        </div>
    </div>
