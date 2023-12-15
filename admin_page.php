<?php
include('config.php');
session_start();
// Seller ID (you may get this from the login session or another source)
$userid = $_SESSION['userid']; // Replace with the actual seller_id
if (!$userid) {
    // 處理沒有提供用戶 ID 的情況
    echo "User ID is missing.";
    exit;
}
//echo $userid ;
$profile_picture = 'profile.jpg';

try {
    //echo $owner_id;
    $sql = "SELECT userid, email, role, phone_number
        FROM users
        WHERE userid = :userid";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();
   

    // Fetch all products as an associative array
    $userInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //echo $storeid[0]['storeid'];
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

try{
    $sql = "SELECT s.storeid
            FROM users As u
            JOIN Store As s ON s.ownerid = u.userid
            WHERE s.ownerid = :userid";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();

    // Fetch all products as an associative array
    $storeid = $stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// 網頁開始
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $userInfo['email']; ?>'s Profile</title>
    <link rel="stylesheet" href="style1.css">
    <style>
    .profile-container {
        text-align: center;
    }

    .profile-picture {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background-image: url('<?php echo $profile_picture; ?>');
        background-size: cover;
        margin-bottom: 20px;
        overflow: hidden;
        display: inline-block;
    }

        
    </style>
</head>
<body>
    <!--上面選單 -->
    <h1>TradeOrbit</h1>
    <nav>
        <ul class="flex-nav">
            <li class="active"><a href="admin_product_page.php">Home page</a></li>
            <li class="active"><a href="admin_page.php">My profile</a></li>
            <li class="active"><a href="admin_see_user.php">User and Market Information</a></li>
        </ul>
    </nav>
    <h2>My profile</h2>
    <div class="profile-container">
        <div class="profile-picture"></div>
        <div class = "container">
            <p>Userid: <?php echo $userInfo[0]['userid']; ?></p>
            <p>Email: <?php echo $userInfo[0]['email'];?></p>
            <p>Phone number: <?php echo $userInfo[0]['phone_number'];?></p>
        </div>
    </div>
    <script>
$(function(){
$('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
//點擊時避免跟隨href位置
event.preventDefault();
//避免在點擊時關閉菜單
event.stopPropagation();
if($(this).parent().hasClass('open') == false){ //當class=open為否時
$(this).parent().addClass('open');
}else{
$(this).parent().removeClass('open');
}
});
});
<script>
</body>
</html>
