-- 09/11/2024
ALTER TABLE `purchase_products` ADD `category_id` INT NULL AFTER `name`;
ALTER TABLE `customers` ADD `age` VARCHAR(100) NULL DEFAULT NULL AFTER `name`;
ALTER TABLE `customers` ADD `sex` VARCHAR(100) NULL DEFAULT NULL AFTER `age`;
ALTER TABLE `customers` ADD `date_of_birth` DATE NULL DEFAULT NULL AFTER `sex`;
ALTER TABLE `purchase_inventory_logs` ADD `discount_percentage` DOUBLE(20,2) NULL DEFAULT '0' AFTER `selling_price`;


CREATE INDEX idx_batch_product_department ON purchase_order_products (batch_no, product_id, department_id);


ALTER TABLE `services` ADD `type` INT NULL DEFAULT NULL AFTER `service_sale_price`;


--28-11-2024
ALTER TABLE `sales_orders` ADD `sale_type` INT NULL COMMENT '1=Offline, 2=Online' AFTER `warehouse_id`;
ALTER TABLE `sales_order_products` ADD `sale_type` INT NULL COMMENT '1=Offline, 2=Online' AFTER `batch_no`;
ALTER TABLE `purchase_inventory_logs` ADD `sale_type` INT NULL COMMENT '1=Offline, 2=Online' AFTER `batch_no`;
ALTER TABLE `sale_payments` ADD `sale_type` INT NULL COMMENT '1=Offline, 2=Online' AFTER `customer_id`;
ALTER TABLE `transaction_logs` ADD `sale_type` INT NULL COMMENT '1=Offline, 2=Online' AFTER `discount_amt`;


---30-11-2024
ALTER TABLE `sales_orders` CHANGE `sale_type` `sale_type` INT NOT NULL COMMENT '1=Offline, 2=Online';
ALTER TABLE `sales_order_products` CHANGE `sale_type` `sale_type` INT NOT NULL COMMENT '1=Offline, 2=Online';
ALTER TABLE `cashes` ADD `investment_amt` DOUBLE(20,2) NOT NULL DEFAULT '0.00' AFTER `opening_balance`;
ALTER TABLE `cashes` ADD `withdraw_amt` DOUBLE(20,2) NOT NULL DEFAULT '0.00' AFTER `investment_amt`;
ALTER TABLE `sales_orders` ADD `note` VARCHAR(500) NULL DEFAULT NULL AFTER `next_payment`;

---10-12-204
ALTER TABLE `sales_orders` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `transaction_logs` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `sale_payments` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `sales_order_products` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `user_id`;
ALTER TABLE `purchase_inventory_logs` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `user_id`;

--25-12-204
ALTER TABLE `users` CHANGE `role_id` `role_id` INT NOT NULL DEFAULT '2' COMMENT '1=Super Admin, 2=Admin,\r\n3=employee';
--19-04-2025
ALTER TABLE `pathology_service_details` ADD `report_prepare_status` TINYINT NULL DEFAULT '1' COMMENT '1=No, 2=Yes' AFTER `user_id`;
ALTER TABLE `pathology_test_reports` ADD `pathology_service_detail_id` BIGINT NULL DEFAULT NULL AFTER `interpretation`;