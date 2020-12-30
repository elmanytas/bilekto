FROM debian:stable

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get -y install git ffmpeg zip apache2 libapache2-mod-php php-gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN sed -i "s/80/8080/g" /etc/apache2/ports.conf
RUN sed -i "s/80/8080/g" /etc/apache2/sites-available/000-default.conf
RUN sed -i "s|^.*ErrorLog.*$|        ErrorLog /dev/stderr|g" /etc/apache2/sites-available/000-default.conf
RUN sed -i "s|^.*CustomLog.*$|        CustomLog /dev/stdout combined|g" /etc/apache2/sites-available/000-default.conf

RUN rm -rf /var/www/html/*;git clone https://github.com/netsuso/bilekto /var/www/html/

VOLUME ["/var/www/html/images"]

ADD bilekto.sh /bilekto.sh

CMD ["/bilekto.sh"]

EXPOSE 8080

#USER root
