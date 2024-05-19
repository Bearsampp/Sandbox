<?php
/*
 * Copyright (c) 2022 - 2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

global $bearsamppLang;
?>
<a class = "anchor" name = "apache"></a>
<div class = "row-fluid">
    <div class = "col-lg-12">
        <h1>
            <img src = "<?php echo $bearsamppHomepage->getResourcesPath() . '/img/apache.png'; ?>" />
            <?php echo $bearsamppLang->getValue( Lang::APACHE ); ?>
            <small></small>
        </h1>
    </div>
</div>
<div class = "row-fluid">
    <div class = "col-lg-6">
        <div class = "list-group">
       <span class = "list-group-item apache-checkport">
                <?php echo $getLoader; ?>
                <i class = "fa-solid fa-server"></i> <?php echo $bearsamppLang->getValue( Lang::STATUS ); ?>
       </span>
            <span class = "list-group-item apache-versions">
          <span class = "label-left col-1">
                <i class = 'fa-regular fa-file-code'></i> <?php echo $bearsamppLang->getValue( Lang::VERSIONS ); ?>
          </span>
          <span class = "apache-version-list col-11">
               <?php echo $getLoader; ?>
          </span>
      </span>
            <span class = "list-group-item apache-modulescount">
                <?php echo $getLoader; ?>
                <i class = 'fa-regular fa-file-code'></i> <?php echo $bearsamppLang->getValue( Lang::MODULES ); ?>
      </span>
            <span class = "list-group-item apache-aliasescount">
             <?php echo $getLoader; ?>
              <i class = "fa-solid fa-link"></i> <?php echo $bearsamppLang->getValue( Lang::ALIASES ); ?>
            </span>
            <span class = "list-group-item apache-vhostscount">
                <?php echo $getLoader; ?>
                <i class = "fa-solid fa-globe"></i> <?php echo $bearsamppLang->getValue( Lang::VIRTUAL_HOSTS ); ?>
            </span>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = 'fa-regular fa-file-code'></i> <?php echo $bearsamppLang->getValue( Lang::MODULES ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-moduleslist d-flex flex-wrap mb-2">
            <?php echo $getLoader; ?>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = "fa-solid fa-link"></i> <?php echo $bearsamppLang->getValue( Lang::ALIASES ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-aliaseslist d-flex flex-wrap mb-2">
            <?php echo $getLoader; ?>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = 'fa-solid fa-folder-tree'></i> <?php echo $bearsamppLang->getValue( Lang::MENU_WWW_DIRECTORY ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-wwwdirectory d-flex flex-wrap mb-2">
            <?php echo $getLoader; ?>
        </div>
    </div>
</div>
<div class = "border grid-list mt-3">
    <div class = "row-fluid mt-2">
        <div class = "col-lg-12 section-top">
            <h3><i class = "fa-solid fa-globe"></i> <?php echo $bearsamppLang->getValue( Lang::VIRTUAL_HOSTS ); ?> <small></small></h3>
        </div>
    </div>
    <div class = "row-fluid">
        <div class = "col-lg-12 apache-vhostslist d-flex flex-wrap mb-2">
            <?php echo $getLoader; ?>
        </div>
    </div>
</div>
