-- Bilet Satın Alma Platformu - SQLite Database Şeması
-- Tüm tabloları oluştur

-- Kullanıcılar Tablosu
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(20) NOT NULL DEFAULT 'user', -- 'user', 'company_admin', 'admin'
    company_id INTEGER DEFAULT NULL,
    credit DECIMAL(10,2) DEFAULT 1000.00, -- Başlangıç sanal kredisi
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
);

-- Otobüs Firmaları Tablosu
CREATE TABLE IF NOT EXISTS companies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Seferler Tablosu
CREATE TABLE IF NOT EXISTS trips (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_id INTEGER NOT NULL,
    departure_city VARCHAR(100) NOT NULL,
    arrival_city VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME,
    price DECIMAL(10,2) NOT NULL,
    total_seats INTEGER NOT NULL DEFAULT 40,
    available_seats INTEGER NOT NULL,
    bus_number VARCHAR(20),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Biletler Tablosu
CREATE TABLE IF NOT EXISTS tickets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    trip_id INTEGER NOT NULL,
    seat_number INTEGER NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    coupon_code VARCHAR(50) DEFAULT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    final_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'active', -- 'active', 'cancelled'
    purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    cancelled_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    UNIQUE(trip_id, seat_number)
);

-- İndirim Kuponları Tablosu
CREATE TABLE IF NOT EXISTS coupons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_rate DECIMAL(5,2) NOT NULL, -- Örn: 10.00 = %10
    usage_limit INTEGER NOT NULL,
    used_count INTEGER DEFAULT 0,
    expiry_date DATE NOT NULL,
    is_active INTEGER DEFAULT 1, -- 1: aktif, 0: pasif
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Varsayılan Admin Kullanıcısı (Şifre: admin123)
INSERT INTO users (username, email, password, full_name, role, credit) 
VALUES ('admin', 'admin@platform.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sistem Yöneticisi', 'admin', 0);

-- Örnek Otobüs Firmaları
INSERT INTO companies (name, phone, email, address) VALUES 
('Metro Turizm', '0850 222 34 55', 'info@metroturizm.com.tr', 'İstanbul'),
('Pamukkale Turizm', '0850 333 35 11', 'info@pamukkale.com.tr', 'Ankara'),
('Kamil Koç', '0850 255 05 05', 'info@kamilkoc.com.tr', 'İzmir');

-- Örnek Firma Admin (Şifre: firma123)
INSERT INTO users (username, email, password, full_name, role, company_id, credit) 
VALUES ('metro_admin', 'metro@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Metro Yöneticisi', 'company_admin', 1, 0);

-- Örnek Normal Kullanıcı (Şifre: user123)
INSERT INTO users (username, email, password, full_name, phone, role, credit) 
VALUES ('demo_user', 'user@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo Kullanıcı', '0555 123 45 67', 'user', 1000.00);

-- Örnek Seferler
INSERT INTO trips (company_id, departure_city, arrival_city, departure_date, departure_time, arrival_time, price, total_seats, available_seats, bus_number) VALUES
(1, 'İstanbul', 'Ankara', '2025-10-25', '09:00', '15:00', 250.00, 40, 40, 'MT-001'),
(1, 'İstanbul', 'İzmir', '2025-10-25', '10:30', '18:30', 300.00, 40, 40, 'MT-002'),
(2, 'Ankara', 'Antalya', '2025-10-26', '08:00', '16:00', 350.00, 45, 45, 'PK-101'),
(2, 'İzmir', 'İstanbul', '2025-10-26', '11:00', '19:00', 280.00, 40, 40, 'PK-102'),
(3, 'İstanbul', 'Trabzon', '2025-10-27', '20:00', '08:00', 400.00, 40, 40, 'KK-201');

-- Örnek İndirim Kuponları
INSERT INTO coupons (code, discount_rate, usage_limit, used_count, expiry_date, is_active) VALUES
('YAZ2025', 15.00, 100, 0, '2025-12-31', 1),
('ILKSEFERIM', 20.00, 50, 0, '2025-11-30', 1),
('OGRENCI10', 10.00, 200, 0, '2025-12-31', 1);