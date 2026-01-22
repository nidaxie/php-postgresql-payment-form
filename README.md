PHP & PostgreSQL Ödeme Formu Sistemi

Bu proje, bir ödeme sürecini uçtan uca simüle eden, PHP ve PostgreSQL kullanılarak geliştirilmiş örnek bir web uygulamasıdır.
Kullanıcı bilgileri, kart bilgileri ve POS bazlı tahsilat işlemleri güvenli ve kontrollü şekilde yönetilmektedir.

🚀 Özellikler

Kullanıcı Kaydı
İsim, email, telefon ve TC bilgileri PostgreSQL veritabanına kaydedilir.
Alan bazlı validasyon kuralları uygulanır.
Kart Bilgileri Yönetimi
Kart numarası tam haliyle saklanmaz.
Sadece ilk 6 hane (BIN) ve son 4 hane kaydedilir.
CVV bilgisi kesinlikle veritabanına yazılmaz.

POS & Taksit Sistemi
POS bilgileri (banka, taksit, komisyon) veritabanından dinamik olarak çekilir.
Kart BIN numarasına göre uygun POS ve taksit seçenekleri gösterilir.
Komisyon oranına göre toplam tutar anlık hesaplanır.

Tahsilat İşlemleri
Seçilen POS’a göre tahsilat kaydı oluşturulur.
Net tutar ve komisyonlu toplam tutar ayrı ayrı saklanır.
Gerçekleşen tüm tahsilatlar ayrı bir sayfada listelenir.

İşlem Güvenliği
Tüm kayıt işlemleri PDO Transaction yapısı ile gerçekleştirilir.
Hata durumunda işlemler otomatik olarak geri alınır (rollback).

🛠 Kullanılan Teknolojiler
Backend: PHP 8
Veritabanı: PostgreSQL
Veritabanı Erişimi: PDO
Frontend: HTML5, JavaScript
Validasyon: HTML5 + PHP (preg_match)

📁 Proje Dosya Yapısı
db.php (PostgreSQL bağlantı ayarlarını ve PDO nesnesini içerir.)
form.php (Kullanıcı, kart ve POS bilgilerinin girildiği ödeme formu arayüzü.)
kaydet.php (Kullanıcı, kart ve tahsilat kayıtlarını oluşturur)
tahsilatlar.php(Gerçekleşen tüm tahsilatları kullanıcı ve POS bilgileriyle birlikte listeler.)

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

CREATE TABLE kart_bilgileri (
    id SERIAL PRIMARY KEY,
    kullanici_id INT NOT NULL REFERENCES kullanici(id),
    bin VARCHAR(6) NOT NULL,
    last4 VARCHAR(4) NOT NULL,
    kart_sahibi TEXT NOT NULL,
    skt_ay INT NOT NULL,
    skt_yil INT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO pos_bilgileri(id, pos_adi, taksit, komisyon, aktif) VALUES
(1, 'Ziraat Bankası', 1, 1.50, 1),
(2, 'Ziraat Bankası', 3, 2.80, 1),
(3, 'Ziraat Bankası', 6, 4.50, 1),
(4, 'Garanti BBVA', 1, 1.65, 1),
(5, 'Garanti BBVA', 3, 3.10, 1),
(6, 'Garanti BBVA', 9, 7.50, 1),
(7, 'İş Bankası', 1, 1.60, 1),
(8, 'İş Bankası', 6, 5.00, 1),
(9, 'Akbank', 1, 1.70, 1),
(10, 'Akbank', 12, 10.00, 1),
(11, 'Finansbank', 1, 1.55, 1),
(12, 'Yapı Kredi', 1, 1.60, 1),
(13, 'Halkbank', 1, 1.45, 1);
