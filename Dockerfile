FROM php:7-cli

RUN docker-php-ext-install pdo pdo_mysql

RUN apt update && apt install -y openssh-client
ADD ./.docker/run.sh /usr/sbin/run.sh

RUN ssh-keygen -f ~/tatu-key-ecdsa -t ecdsa -b 521


CMD ["/bin/bash", "-c", "/usr/sbin/run.sh"]