# PHP & PostgreSQL Ödeme Formu Sistemi
Bu proje, bir ödeme sürecini baştan sona simüle eden temel bir web uygulamasıdır.

## Özellikler
* **Kullanıcı Kaydı:** Müşteri bilgileri PostgreSQL veritabanına kaydedilir.
* **Dinamik Taksit Hesaplama:** POS cihazlarına ait komisyon oranları DB'den çekilerek anlık hesaplanır.
* **İşlem Güvenliği:** Veritabanı işlemleri `PDO Transaction` yapısı ile korunur.
* **İlişkisel Veritabanı:** Kullanıcılar, POS bilgileri ve tahsilatlar tabloları Foreign Key ile birbirine bağlıdır.

## Kullanılan Teknolojiler
* **Dil:** PHP 8
* **Veritabanı:** PostgreSQL
* **Kütüphane:** PDO (PHP Data Objects)
* **Frontend:** HTML5, JavaScript

## Proje Dosya Yapısı
db.php: Veritabanı bağlantı ayarları (PDO).

form.php: Kullanıcı bilgilerinin, kart bilgilerinin ve taksit seçeneklerinin yer aldığı arayüz.

kaydet.php: Form verilerini işleyen, kullanıcıyı kaydeden ve tahsilat işlemini gerçekleştiren arka plan (backend) dosyası.

## Veritabanı Kurulumu
Projenin çalışması için gerekli SQL tabloları:

CREATE TABLE kullanici (
    id SERIAL PRIMARY KEY,
    isim VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telno VARCHAR(20),
    tcno VARCHAR(11) NOT NULL,
    aciklama TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE pos_bilgileri (
    id SERIAL PRIMARY KEY,
    pos_adi VARCHAR(100) NOT NULL,
    taksit INT NOT NULL,
    komisyon NUMERIC(5,2) NOT NULL,
    aktif BOOLEAN DEFAULT TRUE
);
CREATE TABLE tahsilatlar (
    id SERIAL PRIMARY KEY,
    kullanici_id INT NOT NULL,
    pos_bilgileri_id INT NOT NULL,
    tutar NUMERIC(10,2) NOT NULL,
    total_tutar NUMERIC(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

CONSTRAINT fk_kullanici
        FOREIGN KEY (kullanici_id)
        REFERENCES kullanici(id)
        ON DELETE CASCADE,

CONSTRAINT fk_pos
        FOREIGN KEY (pos_bilgileri_id)
        REFERENCES pos_bilgileri(id)
);

INSERT INTO pos_bilgileri (pos_adi, taksit, komisyon) VALUES
('Ziraat POS', 1, 0),
('Ziraat POS', 2, 2.50),
('Ziraat POS', 3, 3.50),
('İş Bankası POS', 1, 0),
('İş Bankası POS', 3, 3.10),
('İş Bankası POS', 6, 5.10),
('Garanti POS', 1, 0),
('Garanti POS', 9, 3),
('Garanti POS', 12, 3.75)
