<?php
    require 'db.php';
    $poslar = $db->query(" SELECT id, pos_adi, taksit, komisyon FROM pos_bilgileri")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Ödeme Formu</title>
<style>
    body{font-family:Arial;padding:20px}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #ddd;padding:8px}
    th{background:#f2f2f2}
</style>
</head>
<body>
    <form method="POST" action="kaydet.php">
        <h3>Kullanıcı Bilgileri</h3>
        <input name="isim" placeholder="İsim" required  pattern="[A-Za-zÇçĞğİıÖöŞşÜü\s]+">
        <input name="email" type="email" placeholder="Email" required>
        <input name="tcno" placeholder="TC No" required  inputmode="numeric" pattern="[0-9]{11}" maxlength="11">
        <input name="telno" placeholder="Telefon" required pattern="[0-9]{10,11}" maxlength="11">
        <textarea name="aciklama" placeholder="Açıklama"></textarea>

        <h3>Kart Bilgileri</h3>
        <input name="kart_sahibi" placeholder="kart sahibi" required  pattern="[A-Za-zÇçĞğİıÖöŞşÜü\s]+">
        <input name="kart_no" placeholder="Kart Numarası" maxlength="16" required inputmode="numeric" pattern="[0-9]{16}" oninput="binKontrol(this.value)">
        <input name="cvv" placeholder="CVV" maxlength="3"  inputmode="numeric" pattern="[0-9]{3}" required>
        <input name="ay" placeholder="AA" maxlength="2" required pattern="^(0[1-9]|1[0-2])$">
        <input name="yil" placeholder="YYYY" maxlength="4" required pattern="[0-9]{4}">

        <h3>Tutar</h3>
        <input type="number" id="tutar" name="tutar" step="0.01" required oninput="hesapla()">

        <table>
            <tr>
                <th>Seç</th><th>POS</th><th>Taksit</th><th>Komisyon</th><th>Toplam</th>
            </tr>

            <?php foreach($poslar as $p): ?>
                <tr class="pos" data-banka="<?= $p['pos_adi'] ?>" data-taksit="<?= $p['taksit'] ?>">
                    <td><input type="radio" name="pos_bilgileri_id" value="<?= $p['id'] ?>"></td>
                    <td><?= $p['pos_adi'] ?></td>
                    <td><?= $p['taksit']==1?'Tek Çekim':$p['taksit'].' Taksit' ?></td>
                    <td>%<?= $p['komisyon'] ?></td>
                    <td class="toplam" data-komisyon="<?= $p['komisyon'] ?>">0.00 ₺</td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <button>Ödeme Yap</button>
    </form>

    <script>
        const satirlar = document.querySelectorAll('.pos');

        const binListesi = {
            "123456":"Ziraat Bankası",
            "234567":"Garanti BBVA",
            "345678":"İş Bankası",
            "456789":"Akbank"
        };

        function hesapla(){
            const tutar = parseFloat(document.getElementById('tutar').value) || 0;
            document.querySelectorAll('.toplam').forEach(td=>{
                const k = td.dataset.komisyon;
                td.innerText = (tutar + tutar*k/100).toFixed(2) + " ₺";
            });
        }

        function binKontrol(no){
            no = no.replace(/\D/g,'');
            if(no.length < 6){
                tabloyuSifirla();
                return;
            }

            const banka = binListesi[no.substring(0,6)] || null;
            banka ? bankaModu(banka) : tekCekimModu();
        }

        function bankaModu(banka){
            satirlar.forEach(s=>{
                const goster =
                    s.dataset.banka === banka || Number(s.dataset.taksit) === 1;

                s.style.display = goster ? 'table-row' : 'none';
                if(!goster) s.querySelector('input').checked = false;
            });
        }

        function tekCekimModu(){
            satirlar.forEach(s=>{
                const goster = Number(s.dataset.taksit) === 1;
                s.style.display = goster ? 'table-row' : 'none';
                if(!goster) s.querySelector('input').checked = false;
            });
        }

        function tabloyuSifirla(){
            satirlar.forEach(s=>{
                s.style.display = 'table-row';
            });
        }
    </script>
</body>
</html>
