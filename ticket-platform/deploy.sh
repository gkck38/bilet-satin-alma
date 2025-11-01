#!/bin/bash

################################################################################
# Deployment Script
# Bilet Satın Alma Platformu - Production Deployment
################################################################################

set -e  # Hata durumunda scripti durdur

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Fonksiyonlar
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${CYAN}ℹ️  $1${NC}"
}

print_header() {
    echo ""
    echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}$1${NC}"
    echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

# Banner
clear
echo -e "${BLUE}"
cat << "EOF"
╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║        🚀 Bilet Satın Alma Platformu                        ║
║        Production Deployment Script v1.0                    ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

print_warning "Bu script production ortamına deployment yapar!"
echo ""
read -p "Devam etmek istediğinizden emin misiniz? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_error "Deployment iptal edildi."
    exit 1
fi

# Başlangıç zamanı
START_TIME=$(date +%s)

print_header "Sistem Kontrolleri"

# Docker kontrolü
if ! command -v docker &> /dev/null; then
    print_error "Docker yüklü değil!"
    exit 1
fi
print_success "Docker kurulu"

# Docker Compose kontrolü
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose yüklü değil!"
    exit 1
fi
print_success "Docker Compose kurulu"

# Git kontrolü (opsiyonel)
if command -v git &> /dev/null; then
    print_success "Git kurulu"
    
    # Git branch bilgisi
    BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")
    COMMIT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
    print_info "Branch: $BRANCH | Commit: $COMMIT"
else
    print_warning "Git yüklü değil (opsiyonel)"
fi

print_header "Önceki Container'ları Durdurma"

if docker-compose ps | grep -q "Up"; then
    print_info "Mevcut container'lar durduruluyor..."
    docker-compose down
    print_success "Container'lar durduruldu"
else
    print_info "Çalışan container bulunamadı"
fi

print_header "Veritabanı Yedeği"

if [ -f "database.db" ]; then
    print_info "Veritabanı yedekleniyor..."
    mkdir -p backups
    BACKUP_FILE="backups/database_$(date +%Y%m%d_%H%M%S).db"
    cp database.db "$BACKUP_FILE"
    print_success "Veritabanı yedeklendi: $BACKUP_FILE"
    
    # Eski yedekleri temizle (30 günden eski)
    find backups/ -name "database_*.db" -mtime +30 -delete 2>/dev/null || true
    print_info "Eski yedekler temizlendi (30+ gün)"
else
    print_warning "Veritabanı dosyası bulunamadı"
fi

print_header "Docker Image Oluşturma"

print_info "Docker image oluşturuluyor (--no-cache)..."
if docker-compose build --no-cache; then
    print_success "Docker image oluşturuldu"
else
    print_error "Docker image oluşturulamadı!"
    exit 1
fi

print_header "Container'ları Başlatma"

print_info "Container'lar başlatılıyor..."
if docker-compose up -d; then
    print_success "Container'lar başlatıldı"
else
    print_error "Container'lar başlatılamadı!"
    exit 1
fi

print_header "Veritabanı Kontrolü"

if [ ! -f "database.db" ]; then
    print_warning "Veritabanı bulunamadı, oluşturuluyor..."
    docker exec bilet-platform php init_db.php
    print_success "Veritabanı oluşturuldu"
else
    print_success "Veritabanı mevcut"
fi

print_header "Dosya İzinleri"

print_info "Dosya izinleri ayarlanıyor..."
chmod -R 755 .
chmod 777 database.db 2>/dev/null || true
print_success "İzinler ayarlandı"

print_header "Health Check"

print_info "Uygulama sağlık kontrolü yapılıyor..."
sleep 5

MAX_RETRIES=10
RETRY_COUNT=0

while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if curl -f http://localhost:8080 &> /dev/null; then
        print_success "Uygulama çalışıyor ve erişilebilir!"
        break
    else
        RETRY_COUNT=$((RETRY_COUNT + 1))
        print_warning "Deneme $RETRY_COUNT/$MAX_RETRIES - Bekleniyor..."
        sleep 3
    fi
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
    print_error "Uygulama erişilebilir değil!"
    print_info "Logları kontrol edin: docker-compose logs"
    exit 1
fi

print_header "Container Bilgileri"

echo ""
docker-compose ps
echo ""

print_header "Log Dosyaları"

print_info "Son loglar:"
docker-compose logs --tail=20

print_header "Deployment Özeti"

# Bitiş zamanı
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo ""
print_success "Deployment başarıyla tamamlandı!"
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}📊 Deployment Bilgileri:${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "   ${PURPLE}Tarih:${NC}     $(date '+%Y-%m-%d %H:%M:%S')"
echo -e "   ${PURPLE}Süre:${NC}      ${DURATION} saniye"
if command -v git &> /dev/null; then
    echo -e "   ${PURPLE}Branch:${NC}    $BRANCH"
    echo -e "   ${PURPLE}Commit:${NC}    $COMMIT"
fi
echo -e "   ${PURPLE}Yedek:${NC}     ${BACKUP_FILE:-Yok}"
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}🌐 Uygulama URL:${NC} ${BLUE}http://localhost:8080${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${YELLOW}📚 Faydalı Komutlar:${NC}"
echo -e "   ${GREEN}docker-compose ps${NC}        - Container durumu"
echo -e "   ${GREEN}docker-compose logs -f${NC}   - Canlı loglar"
echo -e "   ${GREEN}docker-compose restart${NC}   - Yeniden başlat"
echo -e "   ${GREEN}docker-compose down${NC}      - Durdur"
echo ""
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
print_success "Deployment tamamlandı! 🎉"
echo ""

# Deployment log kaydet
LOG_DIR="logs"
mkdir -p "$LOG_DIR"
LOG_FILE="$LOG_DIR/deployment_$(date +%Y%m%d_%H%M%S).log"

cat > "$LOG_FILE" << EOL
Deployment Log
==============
Date: $(date '+%Y-%m-%d %H:%M:%S')
Duration: ${DURATION}s
Branch: ${BRANCH:-unknown}
Commit: ${COMMIT:-unknown}
Backup: ${BACKUP_FILE:-none}
Status: SUCCESS
EOL

print_info "Deployment log kaydedildi: $LOG_FILE"