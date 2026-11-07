FROM php:8.5-apache

# تثبيت الحزم والأدوات اللازمة لنظام PHP وقواعد البيانات + تثبيت Node.js 20 لمحرك واتساب QR
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

# تعديل ملف إعدادات أباتشي الافتراضي لضمان قبول الروابط اللطيفة (AllowOverride All)
RUN echo '<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# نسخ Composer من الصورة الرسمية
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع إلى السيرفر
COPY . /var/www/html

# العمل داخل مجلد المشروع وتثبيت حزم PHP وحزم Node.js (لمحرك الواتساب)
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts \
    && npm install --no-audit --no-fund

# إنشاء مجلد قاعدة البيانات ومجلدات التخزين ومجلد جلسة واتساب والتأكد من الصلاحيات
RUN mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache storage/logs storage/app/wa_marketing_qr_auth bootstrap/cache \
    && touch database/database.sqlite \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache database

# ضبط منفذ العمل
EXPOSE 80

# تشغيل محرك واتساب QR تلقائياً في الخلفية بشكل مستمر + تشغيل Apache
CMD ["sh", "-c", "(while true; do node /var/www/html/whatsapp-qr-engine.js >> /var/www/html/storage/logs/whatsapp-qr-engine.log 2>&1; sleep 2; done) & apache2-foreground"]