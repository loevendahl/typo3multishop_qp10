CREATE TABLE tx_multishop_payment_log (
  id int(11) NOT NULL auto_increment,
  orders_id int(11) NOT NULL DEFAULT '0',
  multishop_transaction_id varchar(127) default '',
  provider_transaction_id varchar(127) default '',
  provider varchar(127) default '',
  ip_address varchar(127) default '',
  crdate int(11) NOT NULL default '0',
  raw_data mediumtext NOT NULL,
  PRIMARY KEY (id),
  KEY orders_id (orders_id),
  KEY multishop_transaction_id (multishop_transaction_id)
) ENGINE=InnoDB;