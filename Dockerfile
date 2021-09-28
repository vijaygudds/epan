# xepan master code image
# updates on each run based on entrypoint script

FROM tianon/true
MAINTAINER Gowrav Vishwakarma <gowravvishwakarma@gmail.com>

RUN apt-get install git
RUN cd /avr/www/html && rm * && git init && git remote add origin https://github.com


CMD ""