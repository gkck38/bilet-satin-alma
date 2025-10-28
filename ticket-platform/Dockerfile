# PHP 8.2 Apache image kullan
FROM php:8.2-apache

# Sistem güncellemeleri ve gerekli paketleri yükle
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Apache mod_rewrite'ı aktifleştir
RUN a2enmod rewrite

# Çalışma dizinini ayarla
WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . /var/www/html/

# Veritabanı dizini için izinleri ayarla
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 777 /var/www/html

# Veritabanını başlat
RUN php init_db.php

# Apache yapılandırması
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# Port 80'i aç
EXPOSE 80

# Apache'yi başlat
CMD ["apache2-foreground"]