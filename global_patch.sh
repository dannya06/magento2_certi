#!/bin/bash
cd /home/app/site

######################### START of line patch ############################
# patch security email
# patch -p1 --forward < m2_hotfixes/MDVA-43395_EE_2.4.3-p1_COMPOSER_v1.patch || true
# patch -p1 --forward < m2_hotfixes/MDVA-43443_EE_2.4.2-p2_COMPOSER_v1.patch || true

patch -p1 --forward < m2_hotfixes/0001-patch-for-gql-schema-stitching-2.4.4.patch || true

#Magento 2.4.4
# patch -p1 --forward < m2_hotfixes/0001-patch-for-lusitanian-2.4.4.patch || true
patch -p1 --forward < m2_hotfixes/0001-patch-for-social-login-2.4.4.patch || true
patch -p1 --forward < m2_hotfixes/0001-patch-for-wishlist-2.4.4.patch || true
patch -p1 --forward < m2_hotfixes/MDVA_44887_2_4_4.patch || true
patch -p1 --forward < m2_hotfixes/0001-patch-for-ezimuel.patch || true
patch -p1 --forward < m2_hotfixes/0001-patch-for-controller-graphql.patch || true

######################### END of line patch ##############################
