-- Run this SQL to add the Flow Builder menu entry
-- Connect to your database and run:

INSERT INTO menus (men_description, men_url, men_icon, men_status)
VALUES ('Flow Builder', 'flow', 'feather icon-git-branch', 1);

-- Then assign it to profiles via the menu_profile table:
-- INSERT INTO menu_profile (men_id, pro_id) 
-- VALUES ((SELECT men_id FROM menus WHERE men_url = 'flow'), 1);
-- (pro_id = 1 is admin, adjust as needed)
