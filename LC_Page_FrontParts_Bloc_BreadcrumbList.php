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
require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';

class LC_Page_FrontParts_Bloc_BreadcrumbList extends LC_Page_FrontParts_Bloc {

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * プロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $layout = new SC_Helper_PageLayout_Ex();
        $layout->sfGetPageLayout($this, false, $_SERVER['SCRIPT_NAME'],
            $this->objDisplay->detectDevice());

        $this->arrBreadcrumb[0][0] = array();

        switch ($this->arrPageLayout['url']) {
        case 'products/list.php':
            $category_id = $_GET['category_id'];
            if ($category_id) {
                $this->arrBreadcrumb[0] = self::getBreadcrumbByCategoryId($category_id);
            } else {
                if ($_GET['mode'] == 'search') {
                    $this->current_name = '検索結果';
                } else {
                    $this->current_name = '全商品';
                }
            }
            break;
        case 'products/detail.php':
            $product_id = $_GET['product_id'];
            $arrBreadcrumb = SC_Helper_DB_Ex::sfGetMultiCatTree($product_id);
            $this->arrBreadcrumb = array($arrBreadcrumb[0]);
            $objProduct = new SC_Product_Ex();
            $arrProduct = $objProduct->getDetail($product_id);
            $this->current_name = $arrProduct['name'];
            break;
        case 'index.php':
            $this->current_name = '';
            break;
        default:
            $this->current_name = $this->arrPageLayout['page_name'];
            break;
        }

        $this->arrData = self::loadData();
    }


    function getBreadcrumbByCategoryId($category_id) {
        $arrBreadcrumb = array();

        // 商品が属するカテゴリIDを縦に取得
        $objDb = new SC_Helper_DB_Ex();
        $arrCatID = $objDb->sfGetParents("dtb_category", "parent_category_id", "category_id", $category_id);

        $objQuery = new SC_Query();
        $index_no = 0;
        foreach($arrCatID as $val){
            // カテゴリー名称を取得
            $sql = "SELECT category_name FROM dtb_category WHERE category_id = ?";
            $arrVal = array($val);
            $CatName = $objQuery->getOne($sql, $arrVal);
            if($val != $category_id){
                $arrBreadcrumb[$index_no]['category_name'] = $CatName;
                $arrBreadcrumb[$index_no]['category_id'] = $val;
            } else {
                $this->current_name = $CatName;
            }
            $index_no++;
        }
        return $arrBreadcrumb;
    }

    //設定を取得する必要がある場合はコメントを外す
    function loadData() {
        $arrRet = array();
        $arrData = SC_Plugin_Util_Ex::getPluginByPluginCode("BreadcrumbList");
        if (!SC_Utils_Ex::isBlank($arrData['free_field1'])) {
            $arrRet['css_data'] = $arrData['free_field1'];
        }
        return $arrRet;
    }
}
