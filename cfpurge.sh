#!/bin/bash

# mirasz@icube.us
# 2017.11.08
# Purge CDN (Cloudflare)

curl -X DELETE "https://api.cloudflare.com/client/v4/zones/cd27b90402793f4f18273aeb9ead2343/purge_cache" \
     -H "X-Auth-Email: muliadi@icube.us" \
     -H "X-Auth-Key: cd99213a1037baec39dc57dab093bec0a4c80" \
     -H "Content-Type: application/json" \
     --data '{"purge_everything":true}'
