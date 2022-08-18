#!/bin/bash
cd /home/app/site

######################### START of line patch ############################
# patch security email
patch -p1 --forward < m2_hotfixes/MDVA-43395_EE_2.4.3-p1_COMPOSER_v1.patch || true
patch -p1 --forward < m2_hotfixes/MDVA-43443_EE_2.4.2-p2_COMPOSER_v1.patch || true
patch -p1 --forward < m2_hotfixes/0001-fix-27332816-bug-flushing-cache-fpc-configiration-ca.patch || true

######################### END of line patch ##############################
