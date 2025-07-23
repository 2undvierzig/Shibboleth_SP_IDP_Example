<!DOCTYPE html>
<html>
<head>
    <title>Shibboleth Attribute Test</title>
</head>
<body>
    <h1>Shibboleth Attribute Test</h1>
    
    <h2>Anwendungs-relevante Attribute:</h2>
    <table border="1">
        <tr><th>Attribut</th><th>HTTP-Header</th><th>Wert</th></tr>
        <tr>
            <td>Display Name</td>
            <td>HTTP_DISPLAYNAME</td>
            <td><?= htmlspecialchars($_SERVER['HTTP_DISPLAYNAME'] ?? 'NICHT GESETZT') ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td>HTTP_EMAIL</td>
            <td><?= htmlspecialchars($_SERVER['HTTP_EMAIL'] ?? 'NICHT GESETZT') ?></td>
        </tr>
        <tr>
            <td>Employee Type</td>
            <td>HTTP_EMPLOYEETYPE</td>
            <td><?= htmlspecialchars($_SERVER['HTTP_EMPLOYEETYPE'] ?? 'NICHT GESETZT') ?></td>
        </tr>
    </table>
    
    <h2>Alle Shibboleth-Attribute:</h2>
    <table border="1">
        <?php
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0 || in_array($key, ['REMOTE_USER', 'AUTH_TYPE'])) {
                echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
            }
        }
        ?>
    </table>
    
    <p><a href="/Shibboleth.sso/Logout">Logout</a></p>
</body>
</html>
