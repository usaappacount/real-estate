FROM php:8.5-apache

# تثبيت الحزم اللازمة لنظام PHP وقواعد البيانات + Node.js 20
RUN apt-get update && apt-get install -y \
    git \
    curl \
    ca-certificates \
    gnupg \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# تفعيل إضافات PHP الضرورية
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# تمكين Apache Rewrite Module
RUN a2enmod rewrite

# توجيه أباتشي رسمياً إلى مجلد public الخاص بلارافيل
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# تعديل إعدادات أباتشي لقبول الروابط اللطيفة (AllowOverride All)
RUN echo '<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# نسخ Composer من الصورة الرسمية
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع إلى السيرفر
WORKDIR /var/www/html
COPY . /var/www/html

# إجبار Git على استخدام HTTPS بدلاً من SSH لتفادي فشل تثبيت libsignal في Baileys داخل Docker
RUN git config --global url."https://github.com/".insteadOf ssh://git@github.com/ \
    && git config --global url."https://github.com/".insteadOf git@github.com: \
    && composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts \
    && npm install qrcode pino @whiskeysockets/baileys@6.7.9 --legacy-peer-deps --no-audit --no-fund

# تجهيز مجلدات التخزين وقاعدة البيانات وصلاحياتها بسرعة فائقة دون تعطيل node_modules
RUN mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache storage/logs storage/app/wa_marketing_qr_auth bootstrap/cache public/uploads/properties public/uploads/whatsapp_pdfs \
    && touch database/database.sqlite storage/logs/whatsapp-qr-engine.log /tmp/whatsapp-qr-engine.log \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/public \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /tmp/whatsapp-qr-engine.log

EXPOSE 80

# مسح كاش الواجهات القديمة وتشغيل محرك واتساب QR في الخلفية ثم تشغيل أباتشي
CMD ["sh", "-c", "php artisan view:clear && php artisan config:clear && (while true; do node /var/www/html/whatsapp-qr-engine.js >> /tmp/whatsapp-qr-engine.log 2>&1; sleep 2; done) & apache2-foreground"]