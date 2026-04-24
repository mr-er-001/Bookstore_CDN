ALTER TABLE `sale_invoice` ADD `status_invoice` TINYINT NOT NULL DEFAULT '1' AFTER `discount_type`;
ALTER TABLE `purchase_invoice` ADD `status_invoice` TINYINT NOT NULL DEFAULT '1' AFTER `discount_type`;

