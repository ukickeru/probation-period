FROM php:8.1.3-fpm

# Extensions for PHP
RUN apt-get update \
    && apt-get install -y libpq-dev libonig-dev libxml2-dev zip

RUN docker-php-ext-install pgsql

# Enable Java for in puprose of using this service as a Jenkins slave node
RUN apt-get update \
    && apt-get install -y default-jdk

# Install and config OpenSSH to enable connection between containers
RUN apt-get update \
    && apt-get install -y openssh-server \
    && mkdir /var/run/sshd \
    && echo 'root:password' | chpasswd \
    && sed -ri 's/.*PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd

EXPOSE 22

# Install Symfony server
RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list \
    && apt-get update \
    && apt-get install -y symfony-cli

RUN symfony server:ca:install

# Install Composer, copy project and install project dependencies
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/app

COPY ./ ./

RUN chmod 644 ./bin

RUN composer install

# Run entrypoint script
ENTRYPOINT sh ./start.sh
