<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    try {
        $db->beginTransaction();
        
        #kisi kayıt  
        $isim=trim($_POST['isim']);
        $email=trim($_POST['email']);
        $telno=trim($_POST['telno']);
        $tcno=trim($_POST['tcno']);
        $aciklama=$_POST['aciklama'];
        if (!preg_match("/^[A-Za-zÇçĞğİıÖöŞşÜü\s]+$/", $isim)) {
            throw new Exception("İsim hatalı");
        }

        if (!preg_match("/^[0-9]{11}$/", $tcno)) {
            throw new Exception("TC No hatalı");
        }

        if (!preg_match("/^[0-9]{10,11}$/", $telno)) {
            throw new Exception("Telefon numarası hatalı");
        }

        if (!empty($isim) && !empty($email) && !empty($tcno) && !empty($telno)) {
            $kisiSorgu=$db->prepare("INSERT INTO kullanici(isim,email,telno,tcno,aciklama) VALUES(?,?,?,?,?)");
            $kisiEkle = $kisiSorgu -> execute([$isim, $email, $telno, $tcno, $aciklama]);
        }

        if (!$kisiEkle) {
            throw new Exception("Kullanıcı eklenemedi");
        }else{
            $kullanici_id = $db->lastInsertId();
        }

        #tahsilat kayıt
        $tutar=$_POST['tutar'];
        $pos_bilgileri_id =$_POST['pos_bilgileri_id'];

        $posSorgu = $db->prepare("SELECT komisyon FROM pos_bilgileri WHERE id = ?");
        $posSorgu->execute([$pos_bilgileri_id]);
        $pos = $posSorgu->fetch(); 

        $total_tutar = $tutar + ($tutar * $pos['komisyon'] / 100);

        if (!empty($tutar)) {
            $tahsilatSorgu=$db->prepare("INSERT INTO tahsilatlar(kullanici_id, pos_bilgileri_id, tutar, total_tutar) VALUES(?, ?, ?, ?)");
            $tahsilatEkle = $tahsilatSorgu -> execute([$kullanici_id, $pos_bilgileri_id, $tutar, $total_tutar]);
        }

        #kart bilgileri kaydetme
        $kart_no = preg_replace('/\D/', '', $_POST['kart_no']);
        $kart_sahibi = trim($_POST['kart_sahibi']);
        $skt_ay = (int) $_POST['ay'];
        $skt_yil = (int) $_POST['yil'];
        $bin = substr($kart_no, 0, 6);      
        $last4 = substr($kart_no, -4);   
        
        if (!preg_match("/^[A-Za-zÇçĞğİıÖöŞşÜü\s]+$/", $kart_sahibi)) {
            throw new Exception("Kart sahibi adı hatalı");
        }
        if ($skt_ay < 1 || $skt_ay > 12) {
            throw new Exception("SKT ay hatalı");
        }
        if (!preg_match("/^[0-9]{4}$/", $_POST['yil'])) {
            throw new Exception("SKT yıl hatalı");
        }

        $kartStmt = $db->prepare("INSERT INTO kart_bilgileri (kullanici_id, bin, last4, kart_sahibi, skt_ay, skt_yil) VALUES (:kullanici_id, :bin, :last4, :kart_sahibi, :skt_ay, :skt_yil)");

        if (!$kartStmt->execute([
            'kullanici_id' => $kullanici_id,
            'bin' => $bin,
            'last4' => $last4,
            'kart_sahibi' => $kart_sahibi,
            'skt_ay' => $skt_ay,
            'skt_yil' => $skt_yil
        ])) {
            throw new Exception("Kart bilgisi kaydedilemedi");
        }

        $db->commit();    
        echo "Ödeme ve kullanıcı kaydı başarıyla tamamlandı. Toplam: " . $total_tutar . " ₺";
    }catch (Exception $e) {
        $db->rollBack();
        echo "Hata oluştu: " . $e->getMessage();
    }
}
?>