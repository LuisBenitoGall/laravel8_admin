FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Update packages
RUN apt-get update && apt-get install -y \
    mariadb-client --no-install-recommends \
    && docker-php-ext-install pdo_mysql

# Add calendar php extension
RUN docker-php-ext-install calendar
    
# Add image render php extension 
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
        libjpeg-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd

# Add zip support
RUN docker-php-ext-install zip && docker-php-ext-enable zip

RUN docker-php-ext-enable bcmath.so calendar.so exif.so gd.so mbstring.so opcache.so pcntl.so pdo_mysql.so sodium.so

# Add nodejs and npm support
RUN apt-get install -y nodejs npm

# Set Ram Limit
RUN cd /usr/local/etc/php/conf.d/ && echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini

# Set working directory
WORKDIR /var/www

USER $user

#CMD [ "./config-init.sh", "&&", "php", "artisan", "serve", "--host=0.0.0.0" ]
