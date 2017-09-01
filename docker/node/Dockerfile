FROM ubuntu:14.04

ENV NODE_VERSION 8.4.0

RUN apt-get update && apt-get install -y \
    curl git bzip2 g++ python=2.7.5-5ubuntu3 make

#node and yarn
RUN curl -L -o /tmp/nodejs.tar.gz https://nodejs.org/dist/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64.tar.gz \
    && tar xfvz /tmp/nodejs.tar.gz -C /usr/local --strip-components=1 \
    && rm -r /tmp/nodejs.tar.gz \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN npm install -g yarn

#gosu
RUN apt-get install ca-certificates
RUN gpg --keyserver ha.pool.sks-keyservers.net --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4
RUN curl -o /usr/local/bin/gosu -SL "https://github.com/tianon/gosu/releases/download/1.4/gosu-$(dpkg --print-architecture)" \
    && curl -o /usr/local/bin/gosu.asc -SL "https://github.com/tianon/gosu/releases/download/1.4/gosu-$(dpkg --print-architecture).asc" \
    && gpg --verify /usr/local/bin/gosu.asc \
    && rm /usr/local/bin/gosu.asc \
    && chmod +x /usr/local/bin/gosu

WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
