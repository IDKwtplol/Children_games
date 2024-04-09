<?php
require_once __DIR__ . '/../../config.php'; 

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (username, password) VALUES (?, ?)';

    try {
        $statement = $pdo->prepare($sql);
        $statement->execute([$username, $password]);
        $message = 'User registered successfully!';
    } catch(PDOException $e) {
        $message = 'Error registering user: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    $("#username").keyup(function(){
        var username = $(this).val().trim();
        if(username != ''){
            $.ajax({
                url: 'check_username.php',
                type: 'post',
                data: {username: username},
                success: function(response){
                    $('#username_status').html(response);
                }
            });
        }else{
            $("#username_status").html("");
        }
    });
});
</script>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Register</title>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if ($message): ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div id="username_status"></div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>
