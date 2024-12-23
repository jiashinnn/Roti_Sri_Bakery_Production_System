<?php 
session_start();
include('db_connect.php');

?>

<script>
    var welcomeMessage = 'Welcome <?php echo isset($_SESSION['login_name']) ? $_SESSION['login_name'] : "User"; ?>!';
    alert(welcomeMessage);
</script>

<div class="row">

</div>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            Welcome <?php echo isset($_SESSION['login_name']) ? $_SESSION['login_name'] : "User"; ?>!
        </div>
    </div>
</div>