FROM nginx:1.23.2-alpine

COPY ./nginx/conf.d/default.conf.template /etc/nginx/conf.d/default.conf.template
COPY ./nginx/docker-entrypoint.sh /

ENTRYPOINT ["/docker-entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]