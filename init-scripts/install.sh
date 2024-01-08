#!/bin/bash
set -e

# Install WordPress

wp --allow-root user create admin admin@example.com --user_pass=admin --display_name=admin --role=administrator

# Install WooCommerce and activate your custom plugin
wp --allow-root plugin install advanced-custom-fields --activate
wp --allow-root plugin activate dynamic-acf-fields

exec docker-entrypoint.sh "$@"
