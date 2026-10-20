<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = htmlspecialchars(strip_tags(trim($_POST["name"])));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(strip_tags(trim($_POST["message"])));

    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (empty($message)) {
        $errors[] = "Message cannot be empty.";
    }

    if (empty($errors)) {
        $csvFile = 'contacts.csv';
        
        $fileHandle = fopen($csvFile, 'a');
        
        if ($fileHandle) {
            fputcsv($fileHandle, [$name, $email, $message, date("Y-m-d H:i:s")]);
            fclose($fileHandle);
            
            echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
            echo "<h2 style='color: green;'>Success!</h2>";
            echo "<p>Your message has been safely recorded.</p>";
            echo "<a href='contact.html'>Go Back to Contact Page</a>";
            echo "</div>";
        } else {
            echo "<h2 style='color: red; text-align: center;'>Error: Could not write to the file.</h2>";
        }
    } else {
        echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px; color: red;'>";
        echo "<h2>Validation Errors:</h2><ul style='list-style: none; padding: 0;'>";
        foreach ($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul><br><a href='contact.html'>Go Back and Try Again</a>";
        echo "</div>";
    }
} else {
    echo "<h2 style='text-align: center;'>Error: Form was not submitted using POST.</h2>";
}
?>
