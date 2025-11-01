#!/bin/bash

################################################################################
# Installation Script
# Bilet Satın Alma Platformu - Otomatik Kurulum
################################################################################

set -e

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

clear

echo -e "${BLUE}"
cat << "EOF"
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║        🎫 Bilet Satın Alma Platformu                        ║
║        Otomatik Kurulum Scripti v1.0                        ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

echo ""
echo -e "${YELLOW}Bu script, projeyi otomatik olarak kuracaktır.${NC}"
echo -e "${YELLOW}Gereksinimler: Docker ve Docker Compose${NC}"
echo ""
read -p "Devam etmek istiyor musunuz? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}❌ Kurulum iptal edildi.${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}⚙️  Kurulum başlatılıyor...${NC}"
echo ""

# 1. Sistem kontrolü
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}[1/6] Sistem kontrolleri yapılıyor...${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker yüklü değil!${NC}"
    echo -e "${YELLOW}Docker'ı yüklemek için: https://docs.docker.com/get-docker/${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Docker kurulu ($(docker --version))${NC}"

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}❌ Docker Compose yüklü değil!${NC}"
    echo -e "${YELLOW}Docker Compose'u yüklemek için: https://docs.docker.com/compose/install/${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Docker Compose kurulu ($(docker-compose --version))${NC}"

# Docker daemon kontrolü
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker daemon çalışmıyor!${NC}"
    echo -e "${YELLOW}Docker'ı başlatın: sudo systemctl start docker${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Docker daemon çalışıyor${NC}"

# 2. Dosya izinleri
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}[2/6] Dosya izinleri ayarlanıyor...${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

chmod +x deploy.sh 2>/dev/null || true
chmod +x install.sh 2>/dev/null || true
chmod -R 755 .
echo -e "${GREEN}✅ İzinler ayarlandı${NC}"

# 3. Environment dosyası
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}[3/6] Çevre değişkenleri kontrol ediliyor...${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}✅ .env dosyası oluşturuldu${NC}"
    else
        echo -e "${YELLOW}⚠️  .env.example bulunamadı, atlanıyor...${NC}"
    fi
else
    echo -e "${GREEN}✅ .env dosyası mevcut${NC}"
fi

# 4. Eski container'ları temizle
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}[4/6] Eski container'lar temizleniyor...${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

docker-compose down 2>/dev/null || true
echo -e "${GREEN}✅ Eski container'lar temizlendi${NC}"

# 5. Docker Image
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}[5/6] Docker image oluşturuluyor...${NC}"
echo -e "${BLUE}⏳ Bu işlem birkaç dakika sürebilir...${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if docker-compose build --no-cache; then
    echo -e "${GREEN}✅ Docker image başarıyla oluşturuldu${NC}"
else
    echo -e "${RED}❌ Docker image oluşturulamadı!${NC}"
    exit 1
fi

# 6. Veritabanı
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}[6/6] Veritabanı oluşturuluyor...${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if [ -f "database.db" ]; then
    read -p "Mevcut veritabanı silinsin mi? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm database.db
        echo -e "${GREEN}✅ Eski veritabanı silindi${NC}"
    fi
fi

php init_db.php
echo -e "${GREEN}✅ Veritabanı oluşturuldu${NC}"

# Container'ı başlat
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}Uygulama başlatılıyor...${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

if docker-compose up -d; then
    echo -e "${GREEN}✅ Uygulama başlatıldı${NC}"
else
    echo -e "${RED}❌ Uygulama başlatılamadı!${NC}"
    exit 1
fi

# Bekleme
echo ""
echo -e "${BLUE}⏳ Container'ın hazır olması bekleniyor...${NC}"
sleep 5

# Health Check
if curl -f http://localhost:8080 &> /dev/null; then
    echo -e "${GREEN}✅ Uygulama çalışıyor!${NC}"
else
    echo -e "${YELLOW}⚠️  Uygulama henüz erişilebilir değil${NC}"
    echo -e "${YELLOW}Birkaç saniye bekleyip tekrar deneyin: http://localhost:8080${NC}"
fi

# Başarı mesajı
echo ""
echo -e "${GREEN}"
cat << "EOF"
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║        ✅ KURULUM BAŞARIYLA TAMAMLANDI!                     ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}🌐 Uygulama URL:${NC} ${BLUE}http://localhost:8080${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${YELLOW}👤 Demo Hesaplar:${NC}"
echo -e "   ${PURPLE}Admin:${NC}       ${GREEN}admin${NC} / ${GREEN}admin123${NC}"
echo -e "   ${PURPLE}Firma Admin:${NC} ${GREEN}metro_admin${NC} / ${GREEN}firma123${NC}"
echo -e "   ${PURPLE}Kullanıcı:${NC}   ${GREEN}demo_user${NC} / ${GREEN}user123${NC}"
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}📚 Faydalı Komutlar:${NC}"
echo -e "   ${GREEN}make start${NC}      - Uygulamayı başlat"
echo -e "   ${GREEN}make stop${NC}       - Uygulamayı durdur"
echo -e "   ${GREEN}make logs${NC}       - Logları görüntüle"
echo -e "   ${GREEN}make restart${NC}    - Yeniden başlat"
echo -e "   ${GREEN}make shell${NC}      - Container'a gir"
echo -e "   ${GREEN}make clean${NC}      - Temizlik yap"
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${GREEN}🎉 İyi kullanımlar!${NC}"
echo ""