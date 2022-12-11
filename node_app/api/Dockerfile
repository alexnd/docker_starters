# syntax=docker/dockerfile:1.4

FROM node:lts-alpine

ARG API_PORT
ARG NGINX_PORT

EXPOSE 3000

RUN apk add --no-cache bash

COPY <<EOF /etc/app.api.env
APP_URL=http://localhost:${NGINX_PORT}
API_URL=http://api:${API_PORT}
NGINX_PORT=$NGINX_PORT
API_PORT=$API_PORT
EOF

COPY ./api/docker-entrypoint.sh /

WORKDIR /app

#ENV PATH /app/node_modules/.bin:$PATH
#COPY ./src/package*.json ./
#RUN npm install

#COPY ./src .

ENTRYPOINT ["/docker-entrypoint.sh"]

#CMD ["npm", "start"]
CMD ["node", "app.js"]