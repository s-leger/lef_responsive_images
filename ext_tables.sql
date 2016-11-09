CREATE TABLE tt_content (
    image_rendering int(11) unsigned DEFAULT '0' NOT NULL,
    image_cssselector varchar(255) DEFAULT '' NOT NULL,
    images_layout tinyint(3) unsigned DEFAULT '0' NOT NULL,
    images_quality tinyint(3) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'sys_file_reference'
#
CREATE TABLE sys_file_reference (
  alternativefile int(11),
  alternativetag varchar(40) DEFAULT ''
);