FROM php:8.2-apache

# تثبيت الحزم والأدوات اللازمة لنظام PHP وقواعد البيانات
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# تفعيل إضافات PHP الضرورية
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# تمكين Apache Rewrite Module
RUN a2enmod rewrite

# ضبط المجلد الجذر ليكون public (الخاص بلاريفيل)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# نسخ Composer من الصورة الرسمية
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع إلى السيرفر
COPY . /var/www/html

# العمل داخل مجلد المشروع وتثبيت الحزم عبر Composer
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# تعيين صلاحيات المجلدات (خصوصاً storage و bootstrap/cache)
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# ضبط منفذ العمل (Render يزود المنفذ تلقائياً)
EXPOSE 80

# تشغيل Apache في الخلفية
CMD ["apache2-foreground"]