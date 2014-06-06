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

class BreadcrumbList extends SC_Plugin_Base {

    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        // プラグインのロゴ画像をアップ
        if (file_exists(PLUGIN_UPLOAD_REALDIR ."BreadcrumbList/logo.png")){
            if(copy(PLUGIN_UPLOAD_REALDIR . "BreadcrumbList/logo.png", PLUGIN_HTML_REALDIR . "BreadcrumbList/logo.png") === false);
        }
        self::insertFreeField();
    }

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {
        // ロゴ画像削除
        if (file_exists(PLUGIN_HTML_REALDIR ."BreadcrumbList/logo.png")){
            if(SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "BreadcrumbList/logo.png") === false);
        }
    }

    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
        self::insertBloc($arrPlugin);
        copy(PLUGIN_UPLOAD_REALDIR .$arrPlugin['plugin_code']."/templates/breadcrumblist.tpl",
            TEMPLATE_REALDIR . "frontparts/bloc/breadcrumblist.tpl");

        copy(PLUGIN_UPLOAD_REALDIR .$arrPlugin['plugin_code']."/bloc/breadcrumblist.php",
            HTML_REALDIR . "frontparts/bloc/breadcrumblist.php");
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
        // nop
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrBlocId = $objQuery->getCol('bloc_id', "dtb_bloc", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "breadcrumblist"));
        $bloc_id = (int) $arrBlocId[0];
        // ブロックを削除する.
        $where = "bloc_id = ?";
        $objQuery->delete("dtb_bloc", $where, array($bloc_id));
        $objQuery->delete("dtb_blocposition", $where, array($bloc_id));
        SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . "frontparts/bloc/breadcrumblist.tpl");
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR  . "frontparts/bloc/breadcrumblist.php");
    }

    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     *
     * @param SC_Helper_Plugin $objHelperPlugin
     */
    function register(SC_Helper_Plugin $objHelperPlugin) {
        //$objHelperPlugin->addAction('outputfilterTransform', array($this, 'outputfilterTransform'));
        return parent::register($objHelperPlugin, $priority);
    }

    // プラグイン独自の設定データを追加
    function insertFreeField() {
        //設定値を書き込む必要がある場合はコメントを外してください
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $sqlval = array();
        $sqlval['free_field1'] = "
div.breadcrumb {
  padding:0 0 5px;
}
div.breadcrumb div {
  display: inline;
}
            "; // 初期cssデータ
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = ?";
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where, array('BreadcrumbList'));
    }

    function insertBloc($arrPlugin) {
        //ブロックを挿入する必要がある場合はコメントを外してください
        $objQuery = SC_Query_Ex::getSingletonInstance();
        // dtb_blocにブロックを追加する.
        $sqlval_bloc = array();
        $sqlval_bloc['device_type_id'] = DEVICE_TYPE_PC;
        $sqlval_bloc['bloc_id'] = $objQuery->max('bloc_id', "dtb_bloc", "device_type_id = " . DEVICE_TYPE_PC) + 1;
        $sqlval_bloc['bloc_name'] = $arrPlugin['plugin_name'];
        $sqlval_bloc['tpl_path'] = "breadcrumblist.tpl";
        $sqlval_bloc['filename'] = "breadcrumblist";
        $sqlval_bloc['create_date'] = "CURRENT_TIMESTAMP";
        $sqlval_bloc['update_date'] = "CURRENT_TIMESTAMP";
        $sqlval_bloc['php_path'] = "frontparts/bloc/breadcrumblist.php";
        $sqlval_bloc['deletable_flg'] = 0;
        $sqlval_bloc['plugin_id'] = $arrPlugin['plugin_id'];
        $objQuery->insert("dtb_bloc", $sqlval_bloc);
    }
}
