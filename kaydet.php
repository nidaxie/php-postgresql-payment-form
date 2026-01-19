<?php
require 'db.php';

if($_POST){
    try {
        $db->beginTransaction();
        
        #kisi kayıt  
        $isim=$_POST['isim'];
        $email=$_POST['email'];
        $telno=$_POST['telno'];
        $tcno=$_POST['tcno'];
        $aciklama=$_POST['aciklama'];

        if (!empty($isim) && !empty($email) && !empty($tcno) && !empty($telno)) {
            $kisiSorgu=$db->prepare("INSERT INTO kullanici(isim,email,telno,tcno,aciklama) VALUES(?,?,?,?,?)");
            $kisiEkle = $kisiSorgu -> execute([$isim, $email, $telno, $tcno, $aciklama]);
        }
        $kullanici_id = $db->lastInsertId();

        #tahsilat kayıt
        $tutar=$_POST['tutar'];
        $pos_bilgileri_id =$_POST['pos_bilgileri_id'];

        $posSorgu = $db->prepare("SELECT komisyon FROM pos_bilgileri WHERE id = ?");
        $posSorgu->execute([$pos_id]);
        $pos = $posSorgu->fetch(); 

        $total_tutar = $tutar + ($tutar * $pos['komisyon'] / 100);

        if (!empty($tutar)) {
            $tahsilatSorgu=$db->prepare("INSERT INTO tahsilatlar(kullanici_id, pos_bilgileri_id, tutar, total_tutar) VALUES(?, ?, ?, ?)");
            $tahsilatEkle = $tahsilatSorgu -> execute([$kullanici_id, $pos_bilgileri_id, $tutar, $total_tutar]);
        }
        $db->commit();    
        echo "Ödeme ve kullanıcı kaydı başarıyla tamamlandı. Toplam: " . $total_tutar . " ₺";
    }catch (Exception $e) {
        $db->rollBack();
        echo "Hata oluştu: " . $e->getMessage();
    }
}
?>