FROM ubuntu:14.04

ENV PATH /usr/local/rvm/bin:$PATH

RUN apt-get update \
    && apt-get install -y \
        curl

#RVM
RUN gpg --keyserver hkp://keys.gnupg.net --recv-keys D39DC0E3 \
    && curl -L https://get.rvm.io | /bin/bash -s stable
RUN ./usr/local/rvm/scripts/rvm

RUN mkdir -p /var/www/html
RUN sed -ie "s/`id -u www-data`/`stat -c %u /var/www/html`/g" /etc/passwd

WORKDIR /var/www/html/
#RUN "rvm install `cat .ruby-version`"
