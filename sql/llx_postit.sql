-- Copyright (C) 2022 SuperAdmin
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_postit(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    entity integer DEFAULT 1 NOT NULL,
	label varchar(255),
	fk_object integer NOT NULL,
    fk_actioncomm integer NOT NULL,
	fk_user integer NOT NULL ,
	fk_user_todo integer NOT NULL,
	fk_postit integer NOT NULL,
	position_top double(24,8) NOT NULL,
	position_left double(24,8) NOT NULL,
	position_width double(24,8) NOT NULL,
	position_height double(24,8) NOT NULL,
	date_creation datetime NOT NULL,
	tms timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	type_object varchar(50),
	status varchar(50),
	comment longtext,
	title varchar(255),
	color varchar(255),
    import_key varchar(14)
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
