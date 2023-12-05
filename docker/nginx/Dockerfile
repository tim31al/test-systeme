FROM nginx:alpine

RUN apk update && apk upgrade && apk add --no-cache bash

ARG UID
ARG GID
ARG USER
ARG GROUP

RUN addgroup -g ${GID} -S ${GROUP} \
    && adduser -u ${UID} -S -D -H ${USER} ${GROUP}

EXPOSE 80

COPY nginx.conf /etc/nginx/
COPY default.conf /etc/nginx/conf.d/