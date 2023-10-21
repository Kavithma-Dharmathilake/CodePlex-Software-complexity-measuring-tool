<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">



    <!-- Topbar Navbar -->

    <?php

    require 'C:\wamp64\www\CodePlex\config.php';
    $sql = "SELECT total FROM wcc";
    $result = $conn->query($sql);
    $tot1 = 0;
    if ($result->num_rows > 0) {


        while ($row = $result->fetch_assoc()) {
            $tot1++;

        }
    } else {

    }



    ?>





    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow" type="none">
        <a class="nav-link " id="userDropdown">

            <img class="img-profile rounded-circle mr-2" src="img/undraw_profile.svg">
            <span class="mr-5 d-none d-lg-inline text-gray-600 small">Hello Welome to CodePlex</span>
        </a>

    </li>
    <div class="topbar-divider d-none d-sm-block"></div>
    <li class="nav-item dropdown no-arrow" type="none">

        <span class="mr-5 d-none d-lg-inline text-gray-600 small"><?php echo $tot1 ?> Complexities have been checked sor far</span>


    </li>



</nav>
<!-- End of Topbar -->