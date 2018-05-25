ARG ENVIRONMENT=prod
FROM git.jazzy.pro:4567/social-bets/social-bets-docker-bootstrap/${ENVIRONMENT}

ARG ENVIRONMENT
ARG DIR=/var/www/service
WORKDIR $DIR

# install Swoole PHP extension
RUN apt-get update \
    && apt-get --yes install php7.2-zip php-pear php7.2-dev \
    && pecl install swoole \
    && echo extension=swoole.so > /etc/php/7.2/cli/conf.d/20-swoole.ini \
    && echo extension=swoole.so > /etc/php/7.2/fpm/conf.d/20-swoole.ini

# Install PHP extensions
#CMD docker-php-ext-install mbstring

# install dependencies
#COPY composer.json composer.lock ${DIR}/
#RUN echo $ENVIRONMENT | grep -q prod && composer install --no-dev --no-scripts --no-suggest --no-autoloader || composer install --no-scripts --no-suggest --no-autoloader

# copy project files
#COPY . ${DIR}

# Copy parameters file
#RUN cp docker/parameters.yml app/config/parameters.yml

# generate autoload, bootstap file and clear cache

#Set ENVS

#ENV DATABASE_HOST 127.0.0.1
#ENV DATABASE_PORT 3306
#ENV DATABASE_NAME jazzy-wallboard
#ENV DATABASE_USER devel
#ENV DATABASE_PASSWORD devel

#RUN composer dump-autoload \
#    && rm -rf var/logs/* var/cache/* var/sessions/* \
#    && chmod g+s var/logs var/cache var/sessions

EXPOSE 80 443

# clear cache & start supervisor
#CMD bin/console c:c --no-warmup -e $ENVIRONMENT && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
#CMD  /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
