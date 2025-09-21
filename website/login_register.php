<?php
    session_start();
    require_once 'config.php';

    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
        if ($checkEmail->num_rows > 0) {
            $_SESSION['register_error'] = 'email já registrado.';
            $_SESSION['active_form'] = 'register';
        } else {
            $conn->query("INSERT INTO users  (name, email, password) VALUES ('$name', '$email', '$password')");
            // Corrigido: o valor da sessão deve ser 'register' (sem o ponto).
            $_SESSION['active_form'] = 'login';
        }

        header("Location: index.php");
        exit();
    }

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statements para evitar SQL Injection (boa prática!)
    $stmt = $conn->prepare("SELECT name, email, password, cargo FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['cargo'] = $user['cargo']; // Armazena o cargo na sessão

            // Redireciona com base no cargo
            if ($user['cargo'] == 'professor') {
                header("Location: professor_page.php");
                exit();
            } else {
                header("Location: user_page.php");
                exit();
            }
        }
    }
    
    $_SESSION['login_error'] = 'email ou senha incorreta.';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}

?>