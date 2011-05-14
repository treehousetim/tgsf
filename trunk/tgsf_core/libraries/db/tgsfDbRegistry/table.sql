/**
* The registry table.  This holds system settings.
*/
CREATE TABLE registry
(
	registry_context			char(32)		NOT NULL,
	registry_key				char(32)		NOT NULL,
	registry_group				char(32)		NOT NULL,
	registry_value				text			DEFAULT NULL,
	registry_type				enum( '', 'text','checkbox','textarea','dropdown','date' ),
	registry_list_values		text			DEFAULT NULL,
	registry_label				char(32)		DEFAULT NULL,
	registry_desc				varchar(255)	DEFAULT NULL,
	registry_help				text			DEFAULT NULL,

	PRIMARY KEY (registry_key,registry_context,registry_group),
	KEY registry_group (registry_group),
	KEY registry_app(registry_app)
) ENGINE=MyISAM;