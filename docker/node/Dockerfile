FROM ubuntu:14.04

ENV NODE_VERSION 5.0.0

RUN apt-get update
RUN apt-get install -y \
    curl

RUN curl -L -o /tmp/nodejs.tar.gz https://nodejs.org/dist/v${NODE_VERSION}/node-v${NODE_VERSION}-linux-x64.tar.gz \
    && tar xfvz /tmp/nodejs.tar.gz -C /usr/local --strip-components=1 \
    && rm -r /tmp/nodejs.tar.gz \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
