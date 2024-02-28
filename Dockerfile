FROM php:8-alpine

# Install latest composer.
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"

# Move Composer for all instances.
RUN mv composer.phar /usr/local/bin/composer
RUN mkdir -p /root/proxmoxve/

# Entrypoint for docker.
ENTRYPOINT ["sh"]

# Workin Dir.
WORKDIR /root/proxmoxve/
