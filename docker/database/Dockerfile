FROM mariadb:10.7

ENV MYSQL_DATABASE booking
ENV MYSQL_ROOT_PASSWORD "secret123"
ENV MYSQL_USER admin
ENV MYSQL_PASSWORD "secret123"

CMD ["mysqld", "--character-set-server=utf8", "--collation-server=utf8_unicode_ci"]

EXPOSE 3306
