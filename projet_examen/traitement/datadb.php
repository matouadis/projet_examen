 <?php 
    $host = "localhost";
    $db_name = "projet_examen"; 
    $user = "root";
    $password ="";

    try{
        $pdo = new PDO("mysql:host=$host; dbname=$db_name", $user , $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $excep){ 
        die("Erreur de connexion " . $excep->getMessage());
    }
?>