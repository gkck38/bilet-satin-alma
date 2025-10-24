# Makefile - Hızlı komutlar için
# Kullanım: make [komut]

.PHONY: help install start stop restart logs clean init-db test

# Varsayılan komut
help:
	@echo "🎫 Bilet Satın Alma Platformu - Yardım"
	@echo ""
	@echo "Kullanılabilir komutlar:"
	@echo "  make install     - Docker image'ı oluştur"
	@echo "  make start       - Uygulamayı başlat"
	@echo "  make stop        - Uygulamayı durdur"
	@echo "  make restart     - Uygulamayı yeniden başlat"
	@echo "  make logs        - Logları görüntüle"
	@echo "  make clean       - Container'ları ve volume'leri temizle"
	@echo "  make init-db     - Veritabanını sıfırla ve baştan oluştur"
	@echo "  make shell       - Container'a bash ile gir"
	@echo "  make test        - Testleri çalıştır"
	@echo "  make backup      - Veritabanını yedekle"
	@echo "  make restore     - Veritabanını geri yükle"
	@echo ""

# Docker kurulumu
install:
	@echo "📦 Docker image oluşturuluyor..."
	docker-compose build

# Uygulamayı başlat
start:
	@echo "🚀 Uygulama başlatılıyor..."
	docker-compose up -d
	@echo "✅ Uygulama başlatıldı: http://localhost:8080"

# Uygulamayı durdur
stop:
	@echo "🛑 Uygulama durduruluyor..."
	docker-compose down
	@echo "✅ Uygulama durduruldu"

# Uygulamayı yeniden başlat
restart: stop start
	@echo "🔄 Uygulama yeniden başlatıldı"

# Logları görüntüle
logs:
	@echo "📋 Loglar görüntüleniyor (Çıkmak için Ctrl+C)..."
	docker-compose logs -f

# Temizlik
clean:
	@echo "🧹 Temizlik yapılıyor..."
	docker-compose down -v
	rm -f database.db
	@echo "✅ Temizlik tamamlandı"

# Veritabanını baştan oluştur
init-db:
	@echo "🗄️ Veritabanı sıfırlanıyor..."
	rm -f database.db
	php init_db.php
	@echo "✅ Veritabanı oluşturuldu"

# Container'a bash ile gir
shell:
	@echo "💻 Container'a bağlanılıyor..."
	docker exec -it bilet-platform bash

# Testleri çalıştır
test:
	@echo "🧪 Testler çalıştırılıyor..."
	@echo "⚠️ Test sistemi henüz yapılandırılmadı"
	# docker exec bilet-platform php vendor/bin/phpunit

# Veritabanını yedekle
backup:
	@echo "💾 Veritabanı yedekleniyor..."
	@mkdir -p backups
	@cp database.db backups/database_$(shell date +%Y%m%d_%H%M%S).db
	@echo "✅ Yedek oluşturuldu: backups/"

# Veritabanını geri yükle
restore:
	@echo "📥 Veritabanı geri yükleniyor..."
	@echo "Mevcut yedekler:"
	@ls -lh backups/
	@read -p "Hangi yedek yüklensin? (dosya adı): " backup_file; \
	cp backups/$$backup_file database.db
	@echo "✅ Veritabanı geri yüklendi"

# Production build
build-prod:
	@echo "🏭 Production build oluşturuluyor..."
	docker-compose -f docker-compose.prod.yml build
	@echo "✅ Production build hazır"

# Güvenlik kontrolü
security-check:
	@echo "🔒 Güvenlik kontrolü yapılıyor..."
	@chmod 600 database.db 2>/dev/null || echo "⚠️ database.db bulunamadı"
	@chmod 755 . 2>/dev/null
	@echo "✅ Güvenlik kontrolü tamamlandı"

# Dosya izinlerini düzelt
fix-permissions:
	@echo "🔧 Dosya izinleri düzenleniyor..."
	@chmod -R 755 .
	@chmod 777 database.db 2>/dev/null || echo "⚠️ database.db bulunamadı"
	@echo "✅ İzinler düzenlendi"

# Composer install
composer-install:
	@echo "📦 Composer paketleri yükleniyor..."
	docker exec bilet-platform composer install
	@echo "✅ Paketler yüklendi"

# Composer update
composer-update:
	@echo "🔄 Composer paketleri güncelleniyor..."
	docker exec bilet-platform composer update
	@echo "✅ Paketler güncellendi"

# Status kontrolü
status:
	@echo "📊 Uygulama durumu:"
	@docker-compose ps

# Tüm kurulum (temizlik + kurulum + başlatma)
fresh: clean install init-db start
	@echo "🎉 Temiz kurulum tamamlandı!"
	@echo "🌐 Uygulama: http://localhost:8080"
	@echo "👤 Admin: admin / admin123"