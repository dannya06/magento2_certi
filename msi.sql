UPDATE cataloginventory_stock_status css
left JOIN cataloginventory_stock_item csi ON css.`product_id` = csi.`product_id`
SET css.`qty` = csi.`qty`, css.`stock_status` = csi.`is_in_stock`
WHERE csi.`qty` IS NOT NULL;

INSERT INTO inventory_source_item(`source_code`,`sku`,`quantity`,`status`)
select * from (
select 'default' as source_code,cp.sku,(qty + (IFNULL(c.quantity,0))) as "quantity", cs.`is_in_stock` as "status"
from cataloginventory_stock_item cs
left join catalog_product_entity cp
on cs.`product_id`=cp.`entity_id`
left join (
select sku,SUM(quantity) as quantity 
from inventory_reservation
group by sku) c
on cp.`sku`=c.`sku`
where cp.`type_id`="simple" and cs.`qty` IS NOT NULL) as c
ON DUPLICATE KEY UPDATE `quantity`=c.`quantity`, `status`=c.`status`;