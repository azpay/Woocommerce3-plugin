FROM l2go/woocommerce:latest
MAINTAINER Bruno Paz "brunopaz@azpay.com.br"
ENV WOOCOMMERCE_VERSION 3.6.4

RUN apt-get update && apt-get install -y apt-transport-https
RUN apt-get install -y  unzip wget mariadb-server supervisor

RUN rm -rf /usr/src/wordpress/wp-content/plugins/woocommerce \
	&& rm -rf /usr/src/wordpress/wp-content/plugins/azpay-woocommerce \
	&& rm -rf /usr/src/wordpress/wp-content/plugins/Woocommerce3-plugin-master

RUN wget https://downloads.wordpress.org/plugin/woocommerce.${WOOCOMMERCE_VERSION}.zip -O /tmp/temp.zip \
    && cd /usr/src/wordpress/wp-content/plugins \
    && unzip /tmp/temp.zip \
    && rm /tmp/temp.zip

RUN wget https://github.com/azpay/Woocommerce3-plugin/archive/master.zip -O /tmp/temp2.zip \
    && cd /usr/src/wordpress/wp-content/plugins \
    && unzip /tmp/temp2.zip \
    && rm /tmp/temp2.zip \
    && cd Woocommerce3-plugin-master \
    && mv azpay-woocommerce ../

COPY supervisor.conf /etc/supervisor/conf.d/supervisor.conf

COPY entrypoint.sh /usr/local/bin/

RUN chmod 755 /usr/local/bin/entrypoint.sh
RUN cp -rp /var/lib/mysql /var/lib/mysql-no-volume

CMD ["supervisord"]