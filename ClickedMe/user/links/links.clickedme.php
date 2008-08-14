<?php
// Load language file
modules_get_language();
pnModLangLoad('ClickedMe', 'user');
user_menu_add_option(pnModURL('ClickedMe','user','main'), _PNCLICKEDMESETTINGS,"modules/ClickedMe/user/pnimages/clickedme.gif");
?>