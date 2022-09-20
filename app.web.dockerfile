FROM nginx:1.10

# Add nginx config file
ADD vhost.conf /etc/nginx/conf.d/default.conf