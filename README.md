# The-Daily-Bulletin
This is a website which stores notices to be shown on specified dates

The website needs to be hosted on a webserver supporting php such as wamp

Some settings might need to be changed to get a server to work properly on a network
Access server through computer ip:
  1. In the server binaries find apache/conf/httpd.conf
  2. There should be a line that starts with "Listen" and then an ip or a number
  3. This should be changed so the entire line says "Listen 80"
