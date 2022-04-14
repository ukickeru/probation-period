FROM php:8.1.3-cli

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Extensions for PHP
RUN apt-get update && apt-get install -y zip libpq-dev libonig-dev libmemcached-dev zlib1g-dev libxml2-dev librabbitmq-dev \
    && pecl install memcached-3.2.0 redis amqp \
    && docker-php-ext-install pgsql pdo_pgsql \
    && docker-php-ext-enable memcached redis amqp

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

# Install nvm, node and npm, and build frontend
ENV NODE_VERSION 14.19.1
WORKDIR /var/app/frontend

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash && \
    export NVM_DIR="$HOME/.nvm" && \
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" && \
    [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" && \
    nvm install $NODE_VERSION && \
    npm install -g @angular/cli --unsafe-perm=true && \
    npm install && \
    npm run-script build

WORKDIR /var/app

# Run entrypoint script
ENTRYPOINT ["/bin/bash", "/var/app/start.sh"]
