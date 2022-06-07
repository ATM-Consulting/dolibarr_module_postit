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


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_postit ADD INDEX idx_postit_rowid (rowid);
ALTER TABLE llx_postit ADD INDEX idx_postit_fk_object (fk_object);
ALTER TABLE llx_postit ADD INDEX idx_postit_fk_actioncomm (fk_actioncomm);
ALTER TABLE llx_postit ADD INDEX idx_postit_fk_user (fk_user);
ALTER TABLE llx_postit ADD INDEX idx_postit_fk_user_todo (fk_user_todo);
ALTER TABLE llx_postit ADD INDEX idx_postit_fk_postit (fk_postit);
ALTER TABLE llx_postit ADD INDEX idx_postit_type_object (type_object);
ALTER TABLE llx_postit ADD INDEX idx_postit_status (status);
-- END MODULEBUILDER INDEXES

