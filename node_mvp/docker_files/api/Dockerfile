FROM node:lts-alpine

EXPOSE 3000

RUN apk add --no-cache bash

WORKDIR /app

ENV PATH /app/node_modules/.bin:$PATH

COPY . .

RUN npm install

CMD node postinstall.js && npm start