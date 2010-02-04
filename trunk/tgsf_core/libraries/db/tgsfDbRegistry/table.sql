/**
* The registry table.  This holds system settings.
*/
CREATE TABLE `registry`
(
	registry_key			char(32)		NOT NULL,
 	registry_group			char(32)		NOT NULL,
 	registry_value			char(64)		DEFAULT NULL,
	registry_label			char(32)		DEFAULT NULL,
	registry_desc			char(128)		DEFAULT NULL,
	registry_input_size		int				DEFAULT 4, /* The width of an input field - visual only */

	PRIMARY KEY ( registry_key, registry_group )
);
