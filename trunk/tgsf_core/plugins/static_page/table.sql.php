<?php

// this is in a php file as a security measure

/*

CREATE TABLE page
(
	page_slug				varchar(255) NOT NULL,
	page_published			bool NOT NULL DEFAULT true,
	page_template			varchar(255) NOT NULL,
	page_title				varchar(255) NOT NULL,
	page_window_title 		varchar(255) DEFAULT NULL,
	page_meta_description	varchar(255) DEFAULT NULL,
	page_content			mediumtext NOT NULL,

	PRIMARY KEY (page_slug),
	KEY page_published (page_published)
) ENGINE = MYISAM ;

*/
