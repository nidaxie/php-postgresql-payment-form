<?php
require 'db.php';
$posStmt=$db->query("SELECT id, pos_adi, taksit, komisyon from pos_bilgileri");
$poslar = $posStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>odeme formu</title>
</head>
<body>
    <form action="kaydet.php" method="POST">
        <h3>Kullanici Bilgileri</h3>
            <input type="text" name="isim" placeholder="İsim" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="tcno" placeholder="TC No" required>
            <input type="text" name="telno" placeholder="Tel No" required>
            <textarea name="aciklama" placeholder="Açıklama"></textarea>
        <br><br>

        <h3>Kart Bilgileri</h3>
            <input type="text" name="kart_no" placeholder="Kart Numarası" maxlength="16" required>
            <input type="text" name="cvv" placeholder="CVV" maxlength="3" required>
            <input type="text" name="son_kullanim_ay" placeholder="AA" maxlength="2"pattern="[0-9]{2}"required>
            <input type="text" name="son_kullanim_yil" placeholder="YYYY" maxlength="4"pattern="[0-9]{2}"required>
        <br><br>

        <h3>Ödeme ve Taksit Seçimi</h3>
            <label>Tutar</label>
            <label>Ödenecek Tutar (₺):</label>
            <input type="number" id="ana_tutar" name="tutar" step="0.01" required oninput="hesapla()">
            <table>
                <tr>
                    <th>Seç</th>
                    <th>POS Adı</th>
                    <th>Taksit</th>
                    <th>Komisyon (%)</th>
                    <th>Toplam Ödeme</th>
                </tr>
                <?php foreach($poslar as $pos): ?>
                <tr>
                    <td><input type="radio" name="pos_bilgileri_id" value="<?= $pos['id'] ?>" required></td>
                    <td><?= $pos['pos_adi'] ?></td>
                    <td><?= $pos['taksit'] ?></td>
                    <td>%<?= $pos['komisyon'] ?></td>
                    <td class="toplam-alan" data-komisyon="<?= $pos['komisyon'] ?>">0.00 ₺</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <button type="submit">Ödeme Yap</button>
    </form>

        <script>
        function hesapla() {
            let tutar = document.getElementById('ana_tutar').value;
            let alanlar = document.querySelectorAll('.toplam-alan');
            
            alanlar.forEach(alan => {
                let komisyon = alan.getAttribute('data-komisyon');
                let toplam = parseFloat(tutar) + (tutar * komisyon / 100);
                alan.innerHTML = isNaN(toplam) ? "0.00 ₺" : toplam.toFixed(2) + " ₺";
            });
        }
        </script>
    

</body>
</html>