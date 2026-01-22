<?php
require "db.php";
$sql = "SELECT k.tcno, k.isim, k.email , k.telno, p.pos_adi, p.taksit, t.tutar, t.total_tutar FROM tahsilatlar t
        JOIN kullanici k ON k.id = t.kullanici_id
        JOIN pos_bilgileri p ON p.id = t.pos_bilgileri_id
        GROUP BY k.tcno, k.isim, k.email , k.telno, p.pos_adi, p.taksit, t.tutar, t.total_tutar";
$tahsilatlar = $db -> query($sql)-> fetchAll();     
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahsilatlar</title>
</head>
<body>
    <table>
        <tr>
            <th>TC No</th><th>İsim</th><th>Email</th><th>Telno</th><th>Banka Adi</th><th>Taksit Sayısı</th><th>Tutar</th><th>Toplam Tutar</th>
        </tr>

        <?php foreach($tahsilatlar as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['tcno']) ?></td>
                    <td><?= htmlspecialchars($t['isim']) ?></td>
                    <td><?= htmlspecialchars($t['email']) ?></td>
                    <td><?= htmlspecialchars($t['telno']) ?></td>
                    <td><?= htmlspecialchars($t['pos_adi']) ?></td>
                    <td><?= htmlspecialchars($t['taksit']) ?></td>
                    <td><?= number_format($t['tutar']) ?> ₺</td>
                    <td><?= number_format($t['total_tutar']) ?> ₺</td>
                </tr>
        <?php endforeach; ?>

    </table>
</body>
</html>