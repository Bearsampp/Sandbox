<a class="anchor" name="mysql"></a>
<div class="row-fluid">
  <div class="col-lg-12">
    <h1><img src="<?php echo $bearsamppHomepage->getResourcesPath() . '/img/mysql.png'; ?>" /> MySQL <small></small></h1>
  </div>
</div>
<div class="row-fluid">
  <div class="col-lg-6">
    <div class="list-group">
      <span class="list-group-item mysql-checkport">
        <?php echo $getLoader; ?>
        <i class="fa-solid fa-server"></i> <?php echo $bearsamppLang->getValue(Lang::STATUS); ?>
      </span>
      <span class="list-group-item mysql-versions col-12">
              <span class="label-left col-1">
                <i class="fa-solid fa-code-merge"></i> <?php echo $bearsamppLang->getValue(Lang::VERSIONS); ?>
              </span>
              <span class="mysql-version-list float-right col-11">
                <?php echo $getLoader; ?>
              </span>
      </span>
    </div>
  </div>
</div>
