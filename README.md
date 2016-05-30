Installation
============

Docker
-----
In order to setup this project in less then two minutes, use
docker and docker-compose. If you do not have them installed, you
can follow our wiki for this virtual machine (Ubuntu Setup) at
http://integration.majisti.com:8083/display/COOK/Installing+Docker+on+Ubuntu+14.04+LTS

Setup
-----
Make sure your nginx proxy is setup to always start on boot:

```
docker run -d -p 80:80 --restart=always -v /var/run/docker.sock:/tmp/docker.sock:ro jwilder/nginx-proxy
```
note: you might want to use *8080:80* or *8081:80* if port 80 or 8080 are already used.

Once this is done, you only need to run:

```
make clean && make
```

Add to your `/etc/host` file:

```
127.0.0.1   majisti.skeleton
```

Browse the site at `http://majisti.skeleton` or `http://majisti.skeleton:port` if you used a different port.

Testing
=======

```
make test
```
