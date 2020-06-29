truncate inventory_reservation;

INSERT INTO inventory_reservation (`stock_id`, `sku`, `quantity`, `metadata`)
SELECT 1 AS `stock_id`, 
soi.sku, 
(0-soi.qty_ordered),
CONCAT('{\"event_type\":\"order_placed\",\"object_typ\e":\"order\",\"object_id\":\"',order_id,'\"}') AS `metadata`
FROM sales_order_item soi
left join sales_order so on soi.`order_id`=so.entity_id
where soi.`product_type`="simple";

INSERT INTO inventory_reservation (`stock_id`, `sku`, `quantity`, `metadata`)
SELECT 1 AS `stock_id`, 
soi.sku, 
soi.qty_ordered,
CONCAT('{\"event_type\":\"shipment_created\",\"object_typ\e":\"order\",\"object_id\":\"',order_id,'\"}') AS `metadata`
FROM sales_order_item soi
left join sales_order so on soi.`order_id`=so.entity_id
where soi.`product_type`="simple" and so.`status` in ("complete","Completed","closed");


INSERT INTO inventory_reservation (`stock_id`, `sku`, `quantity`, `metadata`)
SELECT 1 AS `stock_id`, 
soi.sku, 
soi.qty_ordered,
CONCAT('{\"event_type\":\"order_canceled\",\"object_typ\e":\"order\",\"object_id\":\"',order_id,'\"}') AS `metadata`
FROM sales_order_item soi
left join sales_order so on soi.`order_id`=so.entity_id
where soi.`product_type`="simple" and so.`status`="canceled";
