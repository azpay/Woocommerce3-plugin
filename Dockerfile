FROM l2go/woocommerce:latest
MAINTAINER Bruno Paz "brunopaz@azpay.com.br"

ENV WOOCOMMERCE_VERSION 3.6.4
ENV WOOCOMMERCE_UPSTREAM_VERSION 3.6.4
RUN apt-get update && apt-get install -y apt-transport-https
RUN apt-get install -y  unzip wget

RUN rm -rf /usr/src/wordpress/wp-content/plugins/woocommerce

RUN wget https://downloads.wordpress.org/plugin/woocommerce.${WOOCOMMERCE_VERSION}.zip -O /tmp/temp.zip \
    && cd /usr/src/wordpress/wp-content/plugins \
    && unzip /tmp/temp.zip \
    && rm /tmp/temp.zip

RUN rm -rf /usr/src/wordpress/wp-content/plugins/azpay-woocommerce \
	&& rm -rf /usr/src/wordpress/wp-content/plugins/Woocommerce3-plugin-master


RUN wget https://github.com/azpay/Woocommerce3-plugin/archive/master.zip -O /tmp/temp2.zip \
    && cd /usr/src/wordpress/wp-content/plugins \
    && unzip /tmp/temp2.zip \
    && rm /tmp/temp2.zip \
    && cd Woocommerce3-plugin-master \
    && mv azpay-woocommerce ../