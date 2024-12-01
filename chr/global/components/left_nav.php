<div class="offcanvas offcanvas-start" tabindex="-1" id="left_nav" >
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Appointment Management System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body position-relative">
        <div class="container p-2 profile d-flex">
            <!--
            <div class="w-25 d-flex align-items-center justify-content-center">
                <div class="profilepic left border rounded-circle border-primary-subtle"></div>
            </div>
            -->
            <div class="right h-100 ps-2 w-75 d-flex justify-content-center flex-column">
                <?php
                    echo "<h5 class='profilename'>{$_SESSION['cashier_name']}</h5>";
                    echo "<h6 class='profileid'>{$_SESSION['cashier_id']}</h6>";
                ?>
            </div>
        </div>
        <div class="container px-0 mt-2 d-flex flex-column">
            <div class="features container w-100 p-2 my-1">
                <img src="<?php echo BASE_URL;?>/global/icons/home-2.svg" alt="">
                <a class="outline-none" href="<?php echo BASE_URL;?>/">Scanner</a>
            </div>
            <!--
            <div class="features container w-100 p-2 my-1">
                <img src="<?php echo BASE_URL;?>/global/icons/invoice.svg" alt="">
                <a class="outline-none" href="<?php echo BASE_URL;?>/transaction.php">Transaction Log</a>
            </div>
            -->
        </div>
    </div>
    <div class="container p-2 ps-4 my-1 bg-body border-top" style="height: 3rem;">
        <a class="features d-flex align-items-center outline-none gap-1" href="<?php echo BASE_URL;?>/global/components/logout.php">
            <img src="<?php echo BASE_URL;?>/global/icons/logout.svg" alt="">
            Logout
        </a>
    </div>
</div>