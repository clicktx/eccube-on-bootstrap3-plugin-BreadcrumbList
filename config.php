<?php
/*
 * 2.13系対応パンくずプラグイン
 * パンくずリストを生成する
 * Copyright (C) 2013 Nobuhiko Kimoto
 * info@nob-log.info
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// {{{ requires
require_once PLUGIN_UPLOAD_REALDIR .  'BreadcrumbList/LC_Page_Plugin_BreadcrumbList_Config.php';

// }}}
// {{{ generate page
$objPage = new LC_Page_Plugin_BreadcrumbList_Config();
$objPage->init();
$objPage->process();
